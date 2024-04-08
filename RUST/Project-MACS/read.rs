use chrono::{DateTime, FixedOffset};
use influxdb2::{Client, FromDataPoint};
use influxdb2::models::Query;

#[derive(Debug, FromDataPoint)]
pub struct StockPrice {
    ticker: String,
    value: f64,
    time: DateTime<FixedOffset>,
}

impl Default for StockPrice {
    fn default() -> Self {
        Self {
            ticker: "".to_string(),
            value: 0_f64,
            time: chrono::MIN_DATETIME.with_timezone(&chrono::FixedOffset::east(7 * 3600)),
        }
    }
}

async fn example() -> Result<(), Box<dyn std::error::Error>> {
    let host = std::env::var("InfluxDB").unwrap();
    let org = std::env::var("InfluxOrg").unwrap();
    let token = std::env::var("InfluxToken").unwrap();
    let client = Client::new(host, org, token);

    let qs = format!("from(bucket: \"MACS\")
        |> range(start: -1w)
        |> filter(fn: (r) => r.ticker == \"{}\")
        |> last()
    ", "AAPL");
    let query = Query::new(qs.to_string());
    let res: Vec<StockPrice> = client.query::<StockPrice>(Some(query)).await?;
    println!("{:?}", res);

    Ok(())
}