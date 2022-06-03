(function($, Drupal, drupalSettings){

   Drupal.behaviors.config_form = {
   attach: function (context, settings) {

     setInterval(function() {
        var setTimezone = drupalSettings.timezone_time.timezone;
        var date = new Date ();

          var invdate = new Date(date.toLocaleString('en-US', {
            timeZone: setTimezone
          }));
          let suffix = ['st','nd','rd',...Array(13).fill('th'),'st','nd','rd',Array(7).fill('th'),'st']

            var currentTime = invdate;
            var currentdate = currentTime.getDate();
            var currentmonth =  invdate.toLocaleDateString('en-US',{month:'short'});
            var currentyear = currentTime.getFullYear();
            var currentHours = currentTime.getHours ( );
            var currentMinutes = currentTime.getMinutes ( );
            var currentSeconds = currentTime.getSeconds ( );
            currentMinutes = ( currentMinutes < 10 ? "0" : "" ) + currentMinutes;
            currentSeconds = ( currentSeconds < 10 ? "0" : "" ) + currentSeconds;
            var timeOfDay = ( currentHours < 12 ) ? "AM" : "PM";
            currentHours = ( currentHours > 12 ) ? currentHours - 12 : currentHours;
            currentHours = ( currentHours == 0 ) ? 12 : currentHours;
            var currentTimeString =  currentHours + ":" + currentMinutes + " " + timeOfDay;
            document.getElementById("timer").innerHTML = currentdate+ suffix[currentdate-1] + " " + currentmonth + " "  + currentyear + " - " + currentTimeString;
        }, 1000);
     }
    }
})(jQuery, Drupal, drupalSettings);
