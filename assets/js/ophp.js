var ophp = {

};

$(document).ready(function() {
    ophp.init();
});




ophp.init = function() {
console.info('ophp.init');

    var $elie = $("#intro .logo"), degree = 0, size=100, direction="down", timerRotate, timerPulse;
    Rotate();
    // Pulse();

    function Rotate() {
        $elie.css( { WebkitTransform: 'rotate(' + degree + 'deg)'} );
        $elie.css( { '-moz-transform': 'rotate(' + degree + 'deg)'} );

        timerRotate = setTimeout( function() {
            ++degree; Rotate();
        },15 );

    }

    function Pulse() {
        $elie.css( { 'background-size' : '' + size + '%'} );

        timerPulse = setTimeout( function() {
            if (size > 100) {
                direction = "down"
            }
            if (size < 80) {
                direction = "up"
            }
            if (direction == "up") {
                ++size;
            } else {
                --size;
            }
            Pulse();
        },200 );

    }

    var position = $('.alpha-dog').css('height');


    $('a.bites').bind('mouseover', function() {
        $('.alpha-dog').css('background-position', position);
        dogOut();
    })

    function dogOut() {
        position--;
        timerDog = setTimeout( function() {
            --position; Rotate();
        },15 );
    }




}