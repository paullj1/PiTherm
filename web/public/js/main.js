var gun = new Gun(['http://' + document.location.host + '/gun']);
var client_vars_ref = 'pitherm/browser_vars';
var server_vars_ref = 'pitherm/server_vars';

var app = new Vue({
    el: '#app',
    data: {
      client_vars: {},
      server_vars: {},
      settings_visible: false,
    },
    computed: {
      computed_mode () {
        if (this.server_vars.heat_status == 'on')
          return 'heat';
        if (this.server_vars.cool_status == 'on') 
          return 'cool';
        if (this.client_vars.mode != 'off')
          return 'on';
        return 'off';
      },
      current_temp () { return this.toUnits(this.server_vars.current_temp); },
      current_setpoint () { return this.toUnits(this.client_vars.current_setpoint); },
      unoccupied_heat_set_point () { return this.toUnits(this.client_vars.unoccupied_heat_set_point); },
      unoccupied_cool_set_point () { return this.toUnits(this.client_vars.unoccupied_cool_set_point); },
      night_occupied_heat_set_point () { return this.toUnits(this.client_vars.night_occupied_heat_set_point); },
      night_occupied_cool_set_point () { return this.toUnits(this.client_vars.night_occupied_cool_set_point); },
      day_occupied_heat_set_point () { return this.toUnits(this.client_vars.day_occupied_heat_set_point); },
      day_occupied_cool_set_point () { return this.toUnits(this.client_vars.day_occupied_cool_set_point); },
      units_class () {
        return (this.client_vars.units ? 'celcius' : 'fahrenheit');
      }
    },
    methods: {
      pushNow: function () {
        gun.get(client_vars_ref).put(this.client_vars)
      },
      pushDebounce: _.debounce( function () {
        this.pushNow()
      }, 500),
      showSettings: function (e) {
        this.settings_visible = true;
        e.stopPropagation();
      },
      toUnits: function (temp) {
        if (this.client_vars.units) {
          return parseFloat(temp).toFixed(1);
        }
        return Math.round(temp * 9.0 / 5.0 + 32.0);
      },
      color: function (temp) { // takes value between 4.44 and 37.22
        var h = 240 + Math.floor(((temp - 4.44) / 32.78) * 120);
        return ('color: hsl(' + h + ',100%,50%);');
      }
    }
});


$(document).ready(function() {

  // Initialize gun listeners
  gun.get(client_vars_ref).on(function (data) {
    app.client_vars = data;
  }, false);

  gun.get(server_vars_ref).on(function (data) {
    app.server_vars = data;
  }, false);

  $(window).click(function(e) {
    if (e.target == $('#settings-modal')[0]) {
      app.settings_visible = false;
    }
  });

  var skew = 276/2;

  var flag = 0;
  var polar = {};
  var touchDist = 0;
  var scrollDist = 0;
  var messages_win_visible = false;

  $('#grip-wheel').on('mouseenter', transitionToSetpoint);
  $('#grip-wheel').on('mouseleave', transitionToCurrentTemp);

  $('#grip-wheel').bind('mousewheel', function() {

    var dist = scrollDist + event.deltaY;
    if (Math.abs(dist) > 40) {

      if (!checkCanChangeSetpoint()) {
        scrollDist = 0;
        return;
      }

      if (dist > 0) {
        incrementSetPoint();
      } else { 
        decrementSetPoint();
      }

      scrollDist = 0;
      return;
    }
    scrollDist += event.deltaY;
  });


  $('#grip-wheel').on("touchstart", function() { 
    transitionToSetpoint();
    flag = 1; 
    polar = calcPolar(event);
  });

  $('#grip-wheel').on("touchend", function() { 
    transitionToCurrentTemp();
    flag = 0;
    touchDist = 0;
  });

  $('#grip-wheel').on("touchcancel", function() {
    transitionToCurrentTemp();
    flag = 0; x = 0; y = 0;
  });

  function calcPolar(event) {
    var rect = event.target.getBoundingClientRect();
    var x = (event.targetTouches[0].pageX - rect.left) - skew;
    var y = skew - (event.targetTouches[0].pageY - rect.top);

    r = Math.sqrt(x*x + y*y);
    q = Math.atan2(y,x) * (180 / Math.PI);
    q = (q < 0) ? q + 360 : q;
    return { radius: r, theta: q };
  }


  $('#grip-wheel').on("touchmove", function() {
    event.preventDefault();
    event.stopPropagation();

    if (!checkCanChangeSetpoint()) { return; }

    // ignore multi-touch;
    if (event.touches.length > 1) { return; }

    var new_polar = calcPolar(event);
    if (new_polar.radius < 50 || new_polar.radius > 180) { return; }

    touchDist += (polar.theta - new_polar.theta);
    if (flag == 1 && Math.abs(touchDist) > 30) {

      var clockwise = true;
      // Handle crossover
      if (Math.abs(polar.theta - new_polar.theta) > 180) {
        if (polar.theta < 90 && new_polar.theta > 270) {
          clockwise = true;
        } else {
          clockwise = false;
        }
      } else {
        if (polar.theta - new_polar.theta > 1) {
          clockwise = true;
        } else {
          clockwise = false;
        }
      }

      if (clockwise) { incrementSetPoint(); }
      else           { decrementSetPoint(); }

      polar = new_polar;
      touchDist = 0;
    }
  });

  function checkCanChangeSetpoint() {

    if (app.client_vars.override) {

      if (messages_win_visible) { return; }
      messages_win_visible = true;

      $('#messages').show();
      $('#messages').fadeTo(400, 1.0);

      setTimeout(function() { 
        $('#messages').fadeTo(400, 0.0, function () {
          $('#messages').hide();
          messages_win_visible = false;
        });
      }, 2000);
      return false;
    }
    return true;
  }

  var transitionToCurrentTempTimeout;
  function transitionToCurrentTemp() {
    abortAnimations();
    transitionToCurrentTempTimeout = setTimeout(function() { 
      $('#setpoint').fadeTo(400, 0.0);
      $('#indoor-temp').fadeTo(400, 1.0);
    }, 2000);
  }

  function transitionToSetpoint() {
    abortAnimations();
    $('#indoor-temp').fadeTo(400, 0.0);
    $('#setpoint').fadeTo(400, 1.0);
  }

  function incrementSetPoint() {
    var current_setpoint = app.client_vars.current_setpoint;
    if (current_setpoint + 0.55 > 37.22) {
      flashSetpoint();
    } else {
      app.client_vars.current_setpoint += 0.55;
      app.pushNow();
      rotateWheel();
      0.5555
    }
  }

  function decrementSetPoint() {
    var current_setpoint = app.client_vars.current_setpoint;
    if (current_setpoint - 0.55 < 4.44) {
      flashSetpoint();
    } else {
      app.client_vars.current_setpoint -= 0.55;
      app.pushNow();
      rotateWheel();
    }
  }

  function flashSetpoint() {
    $('#setpoint').css({color: 'red'});
    setTimeout(function() { 
      $('#setpoint').css({ color: "white" });
    }, 100);
  }

  function rotateWheel() {
    $('#grip-wheel').toggleClass('rotated');
  }

  function abortAnimations() {
    clearTimeout(transitionToCurrentTempTimeout);
    $('#setpoint').stop();
    $('#indoor-temp').stop();
  }

});
