$(function () {

    // Init Tooltip bootstrap
    $("[data-toggle='tooltip']").tooltip();

    $(".carto-link a").click(function(e){
    	e.preventDefault();
    	
    	var url = $(this).attr("href");
    	window.open(url, '_blank', "toolbar=no,menubar=no,location=no,height="+screen.height+",width="+screen.width);
    })
});