async fn example() -> Result<(), Box<dyn std::error::Error>> {
    use futures::prelude::*;
    use influxdb2::models::DataPoint;
    use influxdb2::Client;

    let host = std::env::var("InfluxDB").unwrap();
    let org = std::env::var("InfluxOrg").unwrap();
    let token = std::env::var("InfluxToken").unwrap();
    let bucket = "MACS";
    let client = Client::new(host, org, token);
     
    let points = vec![
        DataPoint::builder("cpu")
            .tag("host", "server01")
            .field("usage", 0.5)
            .build()?,
        DataPoint::builder("cpu")
            .tag("host", "server01")
            .tag("region", "us-west")
            .field("usage", 0.87)
            .build()?,
    ];
                                                             
    client.write(bucket, stream::iter(points)).await?;
     
    Ok(())
}