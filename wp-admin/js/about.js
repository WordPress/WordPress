(function($){
    var password = 'Gosh, WordPress is grand.',
        $input = $('#pass'),
        shouldAnimate = true,
        indicatorString = $('#pass-strength-result').text();

    function updateResult(){
        var strength = wp.passwordStrength.meter($input.val(), [], $input.val());

        $('#pass-strength-result').removeClass('short bad good strong');
        switch ( strength ) {
            case 2:
                $('#pass-strength-result').addClass('bad').html( pwsL10n['bad'] );
                break;
            case 3:
                $('#pass-strength-result').addClass('good').html( pwsL10n['good'] );
                break;
            case 4:
                $('#pass-strength-result').addClass('strong').html( pwsL10n['strong'] );
                break;
            default:
                $('#pass-strength-result').addClass('short').html( pwsL10n['short'] );
        }
    }
    function resetMeter(){
        $input.val('');
        $('#pass-strength-result').text(indicatorString);
        $('#pass-strength-result').removeClass('short bad good strong');
    }

    function animate(){
        if (shouldAnimate === false)
            return;
        if ($input.val().length < password.length){
            $input.val( password.substr(0, $input.val().length + 1) );
            updateResult();
        } else {
            resetMeter();
        }
        // Look like real typing by changing the speed new letters are added each time
        setTimeout(animate, 220 + Math.floor(Math.random() * ( 800 - 220)) );
    }
    // 
    function begin(){
        // we async load zxcvbn, so we need to make sure it's loaded before starting 
        if (typeof(zxcvbn) !== 'undefined')
            animate();
        else
            setTimeout(begin,800);
    }
    
    // Turn off the animation on focus
    $input.on('focus', function(){
        shouldAnimate = false;
        resetMeter();
    });

    // Act like a normal password strength meter 
    $input.on('keyup', function(){
        updateResult();
    });

    // Start the animation
    begin();

})(jQuery);
