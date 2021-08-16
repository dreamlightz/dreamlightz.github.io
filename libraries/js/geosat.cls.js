var geosat = geosat || {
    Swipe: function(element = '', action = 'left', callback) {
        if(!element){return null;}
        var min_horizontal_move = 30;
        var max_vertical_move = 30;
        var within_ms = 1000;
        var start_xPos, start_yPos, start_time;
        function touch_start(event) {
            start_xPos = event.touches[0].pageX;
            start_yPos = event.touches[0].pageY;
            start_time = new Date();
        }
        function touch_end(event) {
            var end_xPos = event.changedTouches[0].pageX;
            var end_yPos = event.changedTouches[0].pageY;
            var end_time = new Date();
            let move_x = end_xPos - start_xPos;
            let move_y = end_yPos - start_yPos;
            let elapsed_time = end_time - start_time;
            if (Math.abs(move_x) > min_horizontal_move && Math.abs(move_y) < max_vertical_move && elapsed_time < within_ms) {
                if (move_x < 0 && action === 'left') { if (typeof callback == "function") { callback(); } } 
                else if(move_x > 0 && action !== 'left'){ if (typeof callback == "function") { callback(); } }
            }
        }
        var elem = $(element);
        elem.off('touchstart').on('touchstart', touch_start);
        elem.off('touchend').on('touchend', touch_end);
    },
    IsMoblie: function(){ if( parseInt($(document).width()) <= 800 ){ return true;} else { return false;} },
}