var index = 0;
var data = [];

function makeData(data) // Hace global el array_value asginandole el contenido de los datos de la variable de PHP $data, recibe los datos.
{    
    window.array_data = data;
}

function show() // Se llama a la función show para mostrar la grafica de AMCharts.
{
	let stack = document.getElementById("stack").checked; // Asigna el estado del checkbox con id stack a la variable stack.
	
	let previ = document.getElementById("previ"); // ID del botón previ.
	let next = document.getElementById("next"); // ID del botón next.
	let stackit = document.getElementById("stackit"); // ID de la label stack.

	if (typeof array_data != "undefined") // Si el array array_value contiene datos, es distinto de indefinido.
	{
		if (index > 0) // Si el Índice de los datos es mayor que 0.
		{
			previ.style.visibility = "visible"; // Muestra el botón previ.
		}
		else // Si No.
		{
			previ.style.visibility = "hidden"; // Oculta el botón previ.
		}
		if (index == array_data.length - 1) // Si el Índice es igual al último dato.
		{
			next.style.visibility = "hidden"; // Oculta el botón next.
		}
		if (index < array_data.length - 1) // Si Índice es menor que el último dato
		{
			next.style.visibility = "visible"; // Muestra el botón next.
		}
	}
	else // Si array_value no está definido, no hay datos.
	{
		next.style.visibility = "hidden"; // Oculta el botón next.
		stackit.style.visibility = "hidden"; // Oculta el checkbox stack.
	}

	// Create root element
	// https://www.amcharts.com/docs/v5/getting-started/#Root_element
	var root = am5.Root.new("chartdiv");


	// Set themes
	// https://www.amcharts.com/docs/v5/concepts/themes/
	root.setThemes([
		am5themes_Animated.new(root),
		am5themes_Material.new(root)
	]);


	// Create chart
	// https://www.amcharts.com/docs/v5/charts/xy-chart/
	var chart = root.container.children.push(am5xy.XYChart.new(root, {
	panX: false,
	panY: false,
	wheelX: "panX",
	wheelY: "zoomX",
	paddingLeft: 0,
	layout: root.verticalLayout
	}));

	// Add scrollbar
	// https://www.amcharts.com/docs/v5/charts/xy-chart/scrollbars/
	chart.set("scrollbarX", am5.Scrollbar.new(root, {
	orientation: "horizontal"
	}));

	// Create axes
	// https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
	var xRenderer = am5xy.AxisRendererX.new(root, {
	});
	var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
	categoryField: "fecha",
	renderer: xRenderer,
	tooltip: am5.Tooltip.new(root, {})
	}));

	xRenderer.grid.template.setAll({
	location: 1
	})

	data = getData(index); // Transforma el Objeto multiple en un objeto individual.

	xAxis.data.setAll(data);

	// console.log(data);

	var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
	min: -1,
	renderer: am5xy.AxisRendererY.new(root, {
		strokeOpacity: 0.1
	})
	}));

	// Add legend
	// https://www.amcharts.com/docs/v5/charts/xy-chart/legend-xy-series/
	var legend = chart.children.push(am5.Legend.new(root, {
	centerX: am5.p50,
	x: am5.p50
	}));

	// Add series
	// https://www.amcharts.com/docs/v5/charts/xy-chart/series/
	function makeSeries(name, fieldName) {
	var series = chart.series.push(am5xy.ColumnSeries.new(root, {
		name: name,
		stacked: stack,
		xAxis: xAxis,
		yAxis: yAxis,
		valueYField: fieldName,
		categoryXField: "fecha"
	}));

	series.columns.template.setAll({
		width: am5.percent(80),
		tooltipText: "{name}, {categoryX} - [bold]{valueY}",
		tooltipY: am5.percent(10)
	});

	series.data.setAll(data);

	//No data
	
	var modal = am5.Modal.new(root, {
		content: "La gráfica no tiene datos"
  	});
  
  
  	series.events.on("datavalidated", function(ev) {
		var series = ev.target;
		if (ev.target.data.length < 1) {  
			// Show modal
			modal.open();
		}
	});
	
	// Make stuff animate on load
	// https://www.amcharts.com/docs/v5/concepts/animations/
	series.appear();

	series.bullets.push(function () {
		return am5.Bullet.new(root, {
		sprite: am5.Label.new(root, {
			text: "[#000][bold]{name}: {valueY}",
			fill: root.interfaceColors.get("alternativeText"),
			centerY: am5.p50,
			centerX: am5.p50,
			populateText: true,
			rotation: stack? 0 : -90
		})
		});
	});

	legend.data.push(series);
	}

	makeSeries("Nº Paquetes", "nPaquete");
	makeSeries("Unicast", "unicast");
	makeSeries("Multicast", "multicast");
	makeSeries("Broadcast", "broadcast");
	makeSeries("ARP", "arp");
	makeSeries("Aaaa", "aaaa");
	makeSeries("ICMP", "icmp");
	makeSeries("UDP", "udp");
	makeSeries("TCP", "tcp");
	makeSeries("Otros", "otros");
	makeSeries("IPV6", "ipv6");
	makeSeries("Bbbb", "bbbb");
	makeSeries("Cccc", "cccc");
	makeSeries("SSDP", "ssdp");
	makeSeries("ICPMV6", "icpmv6");

	// Make stuff animate on load
	// https://www.amcharts.com/docs/v5/concepts/animations/
	chart.appear(1000, 100);
}



function getData(index)
{
	let data = [];
	if (typeof array_data != "undefined")
	{
		data[0] = array_data[index];
	}
    return data;
}

function reset(where) // Esta Función Resetea la Gráfica eliminando el div que la contiene y volviendo a crearlo, recibe un dato que puede ser true, false o null, se usa para saber si se precionó el botón next(true), el botón previ(false) o null, se selcciono el checkbox.
{
  const next = document.getElementById("buttons");
  const bodyElement = document.getElementById("view3");
  const div = document. getElementById("chartdiv");
  bodyElement. removeChild(div);

  const container = document.createElement("div");
  container.id = "chartdiv";
  next.before(container);

  if (where != null) // where es null cuando se selecciona o deselecciona el checkbox para apilar/desapilar los datos.
  {
	where == true ? index++ : index--;
  }
  show(); // Llama a la función show(), muestra la gráfica.
}