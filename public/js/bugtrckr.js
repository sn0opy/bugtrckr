/**
 * Main JS code for Bugtrckr
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
 */

$(document).ready(function() {   
    var path = document.location.toString();
    var anchor = '#' + path.split('#')[1];
   
   if(anchor != '#undefined') {
        $(".active").removeClass("active");  
        $(anchor).parent().addClass("active");  
        $(".tabContent").hide();  
        var content_show = $(anchor).attr("title");  
        $("#"+content_show).show();
   }
	
    $(".settingsMenu ul li a").click(function() {
        $(".active").removeClass("active");  
        $(this).parent().addClass("active");  
        $(".tabContent").hide();  
        var content_show = $(this).attr("title");  
        $("#"+content_show).show();  
    }); 
    
    
    $('#projectChooser').change(function() {
       $(this).parent().submit(); 
    });

    $('#delSearch').click(function() {
       $('#searchInput').val("");
       $('#searchForm').submit();
    });

    $('.sortable').tablesorter({
        sortList: [[3,1]] 
    });



    $('.succes').live(function() {
        $(this).fadeOut();
    });
}); 
