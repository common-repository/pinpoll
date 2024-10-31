/**
 * Poll Details Javascript File
 *
 * Descriptoin: Main Javascript File for page "Poll Details" in admin menu
 *
 * @package Pinpoll
 * @subpackage Pinpoll/admin/js
 *
 */

//GLOBAL VARS
var ppCurrentResult = null;
var notValidElemens = [];
var timeout = setTimeout(function() {
  location.reload();
}, 3600000);

var statsTab = jQuery('#pp-tab-votes');
var recosTab = jQuery('#pp-tab-recos');
var statsContainer = jQuery('#pp-stats-votes');
var recosContainer = jQuery('#pp-stats-recos');

var shortcodeTab = jQuery('#pp-tab-embed-shortcode');
var linkTab = jQuery('#pp-tab-embed-link');
var shortcodeContainer = jQuery('#pp-embed-shortcode-body');
var linkContainer = jQuery('#pp-embed-link-body');

if(null !== currentMonth && null !== monthLater) {
  pinpoll_set_timeout_values( currentMonth, monthLater );
}

/**
 * API CALL v1/polls/pollId
 * Description: Receive information about a selected poll form table via API.
 */
jQuery.ajax({
  url : ppBaseURL + '/polls/' + pollIDDetails,
  type : 'GET',
  headers : {
    'Authorization' : jwt
  },
  dataType : 'json',
  success: function(response) {

    //Is Poll active? Show appropriate message
    pinpoll_draw_active_message(response.active === 1 ? true : false, response.timeout);

    //Bar or Pie?
    if(response.stats.lifetime.votes !== 0) {
      pinpoll_draw_chart(response, 'bar');
    } else {
      pinpoll_draw_empty_message();
    }
  },
  error: function(jqXHR, textStatus, errorThrown) {
    console.info('Something went wrong!' + textStatus);
  }
});

/**
 * Post Token to Embed Edit
 * Description: Post current token to embed edit view
 */
window.addEventListener('message', function(evt) {
  try {
    var message = (typeof evt.data === 'string' || evt.data instanceof String) ? JSON.parse(evt.data) : evt.data;
    if(message.action == 'embed:loaded') {
      document.getElementById('pinpoll_' + pollIDDetails).contentWindow.postMessage({
        action : 'set:token',
        'token' : jwt
      },
      '*'
    );
  }else if(message.action == 'embed-edit:submitted' || message.action == 'embed-edit:cancelled') {
      jQuery('#pp-buttons-preview').show();
    }
  } catch(e) {console.log('Exception:'+ e);}
});

/**
 * Draw Chart
 * Description: Draw chart in details (bar or pie)
 *
 * @param  {array}  chartData votes
 * @param  {string} type      bar or pie
 */
function pinpoll_draw_chart(chartData, type) {

  var labels = [];
  var totalSessions = 0;
  var votes = [];
  var colors = [];

  //refresh chart data and init with new values
  if( chartData !== null ) {
    data = chartData;
  }

  //if current chart exists, destroy it
  if( ppCurrentResult !== null ) {
    ppCurrentResult.destroy();
  }

  //init labels for x and y axes in chart
  for (var i = 0; i < data.answers.length; i++) {
    labels.push(data.answers[i].answer);
    votes.push(data.answers[i].votes);
    totalSessions = totalSessions + data.answers[i].votes;
    colors.push(pinpoll_get_colors(i));
  }

  var ctx = document.getElementById('pp-current-result').getContext('2d');

  //draw chart of type bar
  if(type === 'bar') {
    ppCurrentResult = new Chart(ctx, {
      type: 'horizontalBar',
      data: {
        labels : labels,
        datasets : [
          {
            label: "Votes",
              backgroundColor: colors,
              borderColor: colors,
              borderWidth: 1,
              data: votes,
          }
        ]
      },
      options: {
          scales: {
              xAxes: [{
                ticks: {
                  beginAtZero: true,
                  userCallback: function(label, index, labels) {
                    if(Math.floor(label) === label) {
                      return label;
                    }
                  }
                }
              }]
          },
          tooltips: {
            callbacks: {
              label: function(tooltipItem, data) {
                var value = data.datasets[0].data[tooltipItem.index];
                var label = data.labels[tooltipItem.index];
                var percentage = 0;
                if(totalSessions !== 0) {
                  percentage = Math.round(value / totalSessions * 100);
                }
                return 'Votes: ' + value + ' (' + percentage + '%)';
              }
            }
          },
          legend: {
            display: false
          }
      }
    });
    //draw chart of type pie (doughnut)
  } else {
    ppCurrentResult = new Chart(ctx, {
      type: "doughnut",
      data: {
        labels: labels,
        datasets: [
          {
            data: votes,
            backgroundColor: colors,
            hoverBackgroundColor: colors
          }
        ]
      },
      options : {
        tooltips: {
          callbacks: {
            label: function(tooltipItem, data) {
              var value = data.datasets[0].data[tooltipItem.index];
              var label = data.labels[tooltipItem.index];
              var percentage = 0;
              if(totalSessions !== 0) {
                percentage = Math.round(value / totalSessions * 100);
              }
              return label + ': ' + value + ' (' + percentage + '%)';
            }
          }
        }
      }
    });
  }
}

/**
 * Listener: LinkButton #pp-btn-export-chart
 * Description: Exports chart as png in new tab, that the user can downlaod it.
 */
jQuery('#pp-btn-export-chart').click(function() {
  try {
    window.open(ppCurrentResult.toBase64Image());
  } catch(e) {}
});

/**
 * Default Timeout Date
 * Description: Set default timeout values: from = current day; to = 1 week later
 *
 * @param {DateTime} currentMonth Current Month
 * @param {DateTime} monthLater   Month 1 Week Later
 */
function pinpoll_set_timeout_values(currentMonth, monthLater) {
  var curr = document.getElementById('pp-from-month');
  var later = document.getElementById('pp-to-month');

  curr.value = currentMonth;
  later.value = monthLater;
}

/**
 * Empty Message Current Result
 * Description: Draw empty message in chart box if no data available
 */
function pinpoll_draw_empty_message() {
  jQuery('#pp-current-result').hide();
  jQuery('#pp-empty-result').show();
}

/**
 * Active Message
 * Description: Draw active or inactive message in poll detail
 *
 * @param  {Boolean}   isActive   Poll Active?
 * @param  {DateTime}  timeout    Timeout Date
 */
function pinpoll_draw_active_message( isActive, timeout ) {
  if(isActive) {
    jQuery('#pp-poll-active').show();
    if(timeout !== null) {
      document.getElementById('pp-timeout-text').innerHTML = ppDetailsTexts.timeoutMessageFirst + " " + timeout.start_at + " " + ppDetailsTexts.timeoutMessageLast + " " + timeout.timeout_at;
      jQuery('#pp-timeout-info').show();
    }
  } else {
    jQuery('#pp-poll-inactive').show();
  }
}

/**
 * Append Datestrings
 * Description: Helper method to init date vars and append them to valid
 *              date string.
 *
 * @return {string} timeoutData datestring
 */
function pinpoll_get_timeout_data() {
  var fromDay = document.getElementById('pp-from-day').value;
  var fromMonth = document.getElementById('pp-from-month').value;
  var fromYear = document.getElementById('pp-from-year').value;
  var fromHour = document.getElementById('pp-from-hour').value;
  var fromMinute = document.getElementById('pp-from-minute').value;

  var toDay = document.getElementById('pp-to-day').value;
  var toMonth = document.getElementById('pp-to-month').value;
  var toYear = document.getElementById('pp-to-year').value;
  var toHour = document.getElementById('pp-to-hour').value;
  var toMinute = document.getElementById('pp-to-minute').value;

  var fromDate = fromYear + "-" + fromMonth + "-" + fromDay + " " + fromHour + ":" + fromMinute + ":00";
  var toDate = toYear + "-" + toMonth + "-" + toDay + " " + toHour   + ":" + toMinute + ":00";

  var timeoutData = {
    'timeout_start' : fromDate,
    'timeout_end' : toDate
  };

  return timeoutData;
}

/**
 * API CALL v1/polls/{pollid}/timeout
 * Description: Deactivate timeout of poll via API
 */
jQuery('#pp-deactivate-timeout').click(function() {

  //Date which deactivates timeout
  var bodyData = {
    'timeout_start' : '0001-01-01 00:00:00',
    'timeout_end' : '0001-01-01 00:00:00'
  };

  jQuery.ajax({
    url : ppBaseURL + '/polls/' + pollIDDetails + '/timeout',
    type : 'POST',
    headers : {
      'Authorization' : jwt
    },
    data : bodyData,
    dataType : 'json',
    success: function() {
      jQuery('#pp-timeout-info').hide();
    },
    error: function() {
      console.info('Something went wrong in call /timeout');
    }
  });
});

/**
 * API Call v1/polls/{pollid}/timeout
 * Description: Set timeout for poll via API
 */
jQuery('#pp-button-set-timeout').click(function() {
  var isValid = true;
  var isValidTimeRange = true;
  var fromMonthValid = document.getElementById('pp-from-month');
  var toMonthValid = document.getElementById('pp-to-month');
  var dateFields = document.getElementsByClassName('pp-date-field');

  //clear borderColor
  for (var i = 0; i < dateFields.length; i++) {
    //notValidElemens[i].style.borderColor="#ddd";
    dateFields[i].style.borderColor="#ddd";
  }
  fromMonthValid.style.borderColor="#ddd";
  toMonthValid.style.borderColor="#ddd";

  //clear elemens
  notValidElemens = [];

  //field validation
  jQuery('.pp-date-field').each(function() {
    if( isNaN( this.value ) ) {
      isValid = false;
      notValidElemens.push(this);
    }
  });

  //if all fields are valid, do api call
  if(isValid) {
    var bodyData = pinpoll_get_timeout_data();

    if(Date.parse(bodyData.timeout_end) < Date.parse(bodyData.timeout_start)) {
      isValidTimeRange = false;
    }

    if(isValidTimeRange) {
      jQuery.ajax({
        url : ppBaseURL + '/polls/' + pollIDDetails + '/timeout',
        type : 'POST',
        headers : {
          'Authorization' : jwt
        },
        data : bodyData,
        dataType : 'json',
        success: function() {
          jQuery('#pp-input-timeout-container').hide();
          document.getElementById('pp-timeout-text').innerHTML = ppDetailsTexts.timeoutMessageFirst + " " + bodyData.timeout_start + " " + ppDetailsTexts.timeoutMessageLast + " " + bodyData.timeout_end;
          jQuery('#pp-timeout-info').show();
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.info('Something went wrong in call /timeout');
        }
      });
    } else {
      jQuery('.pp-date-field').each(function() {
        this.style.borderColor="red";
      });
      fromMonthValid.style.borderColor="red";
      toMonthValid.style.borderColor="red";
    }

  } else { //else highlite all not valid fields red
    for (var j = 0; j < notValidElemens.length; j++) {
      notValidElemens[j].style.borderColor="red";
    }
  }

});

/**
 * Hide and Show Container
 * Description: Method for showing timeout container
 */
jQuery('#pp-open-timeout').click(function() {
  jQuery('#pp-input-timeout-container').show();
});

/**
 * Hide and Show Container
 * Description: Method for hiding timeout container
 */
jQuery('#pp-button-cancel-timeout-form').click(function() {
  jQuery('#pp-input-timeout-container').hide();
});

/**
 * API CALL v1/polls/{pollid}/activate
 * Description: Deactivate poll via API
 */
jQuery('#pp-deactivate-poll').click(function() {
  var bodyData = { 'active' : '1' };

  jQuery.ajax({
    url: ppBaseURL + '/polls/' + pollIDDetails + '/activate',
    type: 'POST',
    headers: {
      'Authorization' : jwt,
    },
    data : bodyData,
    dataType : 'json',
    success: function(response) {
      console.info('Poll deactivated');
      //hide poll active message
      jQuery('#pp-poll-active').hide();
      jQuery('#pp-input-timeout-container').hide();
      //show poll inactive message
      jQuery('#pp-poll-inactive').show();
    },
    error: function(jqXHR, textStatus, errorThrown) {
      alert('Something went wrong');
    }
  });
});

/**
 * API CALL v1/polls/{pollid}/activate
 * Description: Activate poll via API
 */
jQuery('#pp-activate-poll').click(function() {
  var bodyData = { 'active' : '0' };

  jQuery.ajax({
    url: ppBaseURL + '/polls/' + pollIDDetails + '/activate',
    type: 'POST',
    headers: {
      'Authorization' : jwt,
    },
    data : bodyData,
    dataType : 'json',
    success: function(response) {
      console.info('Poll activated');
      //hide inactive message
      jQuery('#pp-poll-inactive').hide();
      //show inactive message
      jQuery('#pp-poll-active').show();
    },
    error: function(jqXHR, textStatus, errorThrown) {
      alert('Something went wrong');
    }
  });
});

/**
 * Listener: Dropdown Menu for Current Result
 * Description: Show Drowpdown Menu in Chart Section
 */
jQuery('#pp-dropdown-menubtn').click(function(event) {
  event.stopPropagation();
  document.getElementById('pp-dropdown-menu').classList.toggle('pp-dropdown-show');
});

/**
 * Lisetener: Dropdown Menu for Current Result
 * Description: Hide Drowpdown Menu in Chart Section if user clicks anywhere in window
 */
jQuery(document).on('click', function(event) {
  if (!jQuery(event.target).closest('pp-dropdown-menu').length) {
    document.getElementById('pp-dropdown-menu').classList.remove('pp-dropdown-show');
  }
});

/**
 * Lisetener: LinkButton #pp-btn-doughnut
 * Description: Draws pie chart wie current data and hides button "Pie Chart"
 */
jQuery('#pp-btn-doughnut').click(function() {
  if(!jQuery('#pp-current-result').is(':hidden')) {
    pinpoll_draw_chart(null, 'pie');
    if(!jQuery('#pp-btn-doughnut').is(':hidden') && jQuery('#pp-btn-bar').is(':hidden')) {
      jQuery('#pp-btn-doughnut').hide();
      jQuery('#pp-btn-bar').attr('style', 'display:block;');
    }
  }
});

/**
 * Lisetener: LinkButton #pp-btn-bar
 * Description: Draws bar chart wie current data and hides button "Bar Chart"
 */
jQuery('#pp-btn-bar').click(function() {
  if(!jQuery('#pp-current-result').is(':hidden')) {
    pinpoll_draw_chart(null, 'bar');
    if(!jQuery('#pp-btn-bar').is(':hidden')) {
      jQuery('#pp-btn-bar').hide();
      jQuery('#pp-btn-doughnut').show();
    }
  }
});

/**
 * Listener: LinkButton #pp-tab-votes
 * Description: Hide and Show Tabs in Stats Section
 */
jQuery('#pp-tab-votes').click(function() {
  if(!statsTab.hasClass('pp-tabs')) {
    recosTab.removeClass('pp-tabs');
    recosContainer.hide();
    statsTab.addClass('pp-tabs');
    statsContainer.show();
  }
});

/**
 * Listener: LinkButton #pp-tab-recos
 * Description: Hide and Show Tabs in Stats Section
 */
jQuery('#pp-tab-recos').click(function() {
  if(!recosTab.hasClass('pp-tabs')) {
    statsTab.removeClass('pp-tabs');
    statsContainer.hide();
    recosTab.addClass('pp-tabs');
    recosContainer.show();
  }
});

/**
 * Chart Colors
 * Description: Default colors for chart data
 *
 * @param  {int}    index   index of answers
 * @return {array}  colors  colorcode
 */
function pinpoll_get_colors( index ) {
  var colors = [
    "#DB1032", "#13458E", "#FFB600", "#006F58", "#732578", "#FF7D00", "#AEBF29", "#EA0064", "#00B3BE", "#A50032"
  ];

  return colors[index];
}

/**
 * Timeout for Page Refresh
 * Description: Clears timeout if the mouse has moved and sets new timeout.
 */
jQuery(document).mousemove(function() {
  clearTimeout(timeout);
  timeout = setTimeout(function() {
    location.reload();
  }, 3600000);
});

/**
 * Edit Action
 * Description: Trigger if #pp-edit-action is clicked and send edit post
 *              message to embed edit view. Additionally, hides all other buttons
 *              and shows new buttons "Update" and "Cancel".
 */
jQuery('#pp-edit-action').click(function() {
  document.getElementById('pinpoll_' + pollIDDetails).contentWindow.postMessage({
    action : 'set:editmode',
    'token' : jwt
    },
    '*'
  );
  jQuery('#pp-buttons-preview').hide();
});

/**
 * Reset Action
 * Description: Trigger if #pp-reset-action is clicked and call api
 *              v1/polls/pollid/reset
 */
jQuery('#pp-reset-action').click(function() {
  swal({
    title: ppDetailsTexts.swalresettitle,
    text: ppDetailsTexts.swalresettext,
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#C1C1C1',
    confirmButtonText: ppDetailsTexts.swalconfirmreset,

  }).then(function() {
    var date = pinpoll_get_timeout_data();
    jQuery.ajax({
      url: ppBaseURL + '/polls/' + pollIDDetails + '/reset',
      type: 'POST',
      headers: {
        'Authorization' : jwt
      },
      data: {
        'reset_at' : date.timeout_start
      },
      dataType : 'json',
      success: function(response) {
        location.reload();
      },
      error: function(jqXHR, textStatus, errorThrown) {
        if(jqXHR.status === 403) {
          swal(
            ppDetailsTexts.swalerrortitle,
            ppDetailsTexts.unauth + textStatus + ' ' + errorThrown,
            'error'
          );
        } else {
          swal(
            ppDetailsTexts.swalerrortitle,
            ppDetailsTexts.swalerrortext,
            'error'
          );
        }
      }
    });
  }).done();
});

/**
 * Delete Action
 * Description: Trigger if #pp-delete-action is clicked and call api
 *              v1/polls/pollid method = 'DELETE'. If success redirect to
 *              page "Polls".
 */
jQuery('#pp-delete-action').click(function() {
  swal({
    title: ppDetailsTexts.swaldeletetitle,
    text: ppDetailsTexts.swaldeletetext,
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#C1C1C1',
    confirmButtonText: ppDetailsTexts.swalconfirmdelete,

  }).then(function() {
    var date = pinpoll_get_timeout_data();
    jQuery.ajax({
      url: ppBaseURL + '/polls/' + pollIDDetails,
      type: 'DELETE',
      headers: {
        'Authorization' : jwt
      },
      dataType : 'json',
      success: function(response) {
        window.location.href = "admin.php?page=allpolls";
      },
      error: function(jqXHR, textStatus, errorThrown) {
        if(jqXHR.status === 403) {
          swal(
            ppDetailsTexts.swalerrortitle,
            ppDetailsTexts.unauth + textStatus + ' ' + errorThrown,
            'error'
          ).done();
        } else {
          swal(
            ppDetailsTexts.swalerrortitle,
            ppDetailsTexts.swalerrortext,
            'error'
          ).done();
        }
      }
    });
  }).done();
});


function pinpoll_show_embed(){
  var shortcode = '[pinpoll id=' + pollIDDetails + ']';
  var code = '&lt;div data-pinpoll-id=&quot;'+pollIDDetails +'&quot;'+ (ppJSURL!=='https://pinpoll.com'? ('data-location=&quot;' + ppJSURL+'&quot;') : '') +'&gt;&lt;/div&gt;';
  var globalJS = '&lt;script src=&quot;'+ppJSURL+'/global.js&quot; async&gt;&lt;/script&gt;'
  swal({
    title: ppDetailsTexts.swalembedtitle,
    html: ppDetailsTexts.swalembedtext + '<br/><br/><input id="pp-embed-shortcode-preview" type="text" value="' + shortcode + '" onclick="this.setSelectionRange(0, this.value.length)" class="pp-embed-input" readonly="readonly"></input>'+
    '<hr/><br/>' + ppDetailsTexts.swalembedtextWidget + '<br><br><textarea id="pp-embed-code1-preview" type="text" onclick="this.setSelectionRange(0, this.value.length)" class="pp-embed-input-code" readonly="readonly">'+globalJS+code+'</textarea>',
    showCloseButton: true,
    showCancelButton: false,
    confirmButtonText: 'OK'
  }).done();

  var embedinput = document.getElementById('pp-embed-shortcode-preview');
  embedinput.focus();
  embedinput.select();
}

/**
 * Embed Action
 * Description: Trigger if #pp-embed-action is clicked and popup appears
 *              which includes the shortcode of the poll.
 */
jQuery('#pp-embed-action').click(pinpoll_show_embed);

/**
 * Embed Link
 * Description: Trigger if #pp-embed-link is clicked and popup appears which
 *              includes the shortcode of the poll.
 */
jQuery('#pp-embed-link').click(pinpoll_show_embed);

/**
 * Update Poll in Edit Mode
 * Description: Send post message update, so that the iframe updates the poll.
 */
jQuery('#pp-edit-update-action').click(function() {
  document.getElementById('pinpoll_' + pollIDDetails).contentWindow.postMessage({
    action : 'editmode:submit',
    'token' : jwt
    },
    '*'
  );
});


/**
 * Cancel Poll in Edit Mode
 * Description: Send post message cancel, so that the iframe cancels editing
 *              the poll.
 */
jQuery('#pp-edit-cancel-action').click(function() {
  document.getElementById('pinpoll_' + pollIDDetails).contentWindow.postMessage({
    action : 'editmode:cancel'
    },
    '*'
  );
  jQuery('#pp-buttons-preview').show();
});
