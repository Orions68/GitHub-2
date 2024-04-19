use chrono::{DateTime, Utc};
// use influxdb::{Client, Error, InfluxDbWriteable, ReadQuery, Timestamp};
use influxdb::{Client, Error, InfluxDbWriteable, ReadQuery};

#[tokio::main]
// or #[async_std::main] if you prefer
async fn main() -> Result<(), Error> {
    // Connect to db `MACDB` on `http://localhost:8086`
    let client = Client::new("http://localhost:8086", "MACDB").with_token("26Sh0bfFnGUiXGRpCy6w_oeHRqoKdH28Hxa8VQVkjCQPCQzt5uNuJTsDkPo070kIq6kv6NHTdxnRmCus93riAQ==");

    #[derive(InfluxDbWriteable)]
    struct WeatherReading {
        time: DateTime<Utc>,
        humidity: i32,
        #[influxdb(tag)]
        wind_direction: String,
    }

    // Let's write some data into a measurement called `weather`
    let weather_readings = vec![
        WeatherReading {
            // time: Timestamp::Minutes(400000).into(),
            time: Utc::now().into(),
            humidity: 30,
            wind_direction: String::from("north"),
        }
        .into_query("weather"),
        WeatherReading {
            // time: Timestamp::Hours(475000).into(),
            time: Utc::now().into(),
            humidity: 40,
            wind_direction: String::from("west"),
        }
        .into_query("weather"),
        WeatherReading {
            time: Utc::now().into(),
            // time: Timestamp::Hours(480000).into(),
            humidity: 35,
            wind_direction: String::from("northwest"),
        }
        .into_query("weather"),
    ];

    client.query(weather_readings).await?;

    // Let's see if the data we wrote is there
    let read_query = ReadQuery::new("SELECT * FROM weather");

    let read_result = client.query(read_query).await?;
    println!("{}", read_result);
    println!("{:?}", chrono::offset::Local::now());
    Ok(())
}