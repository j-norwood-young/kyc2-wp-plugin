var genChart = function(id) {
	var fixData = function(d) {
		var result = [];
		for(var i in d) {
			if (d[i] > 0)
				result.push([i, d[i]]);
		}
		return result;
	};

	var data = jQuery(id).data("data");
	var chartType = jQuery(id).data("charttype") || "pie";
	var chart = c3.generate({
		data: {
			columns: fixData(data),
			type : chartType,
		},
		bindto: id,
	});
};

document.addEventListener("DOMContentLoaded", function(event) {
	jQuery(".graph").each(function() {
		genChart("#" + jQuery(this).attr("id"));
	});


});