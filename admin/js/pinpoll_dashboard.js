/**
 * Dashboard Javascript File
 *
 * Description: Main Javascript File for page "Dashboard" in admin menu
 *
 * @package Pinpoll
 * @subpackage Pinpoll/admin/js
 *
 */

//GLOBAL VAR FOR CHART
var ppTopFiveChart = null;

/**
 * Statisctic for Dashboard: API CALL v1/stats/top
 * Description: Receive general information about all polls via jQuery
 */
jQuery(document).ready(function() {

  //API CALL FOR STATS
  jQuery.ajax({
    url: ppStatsBaseURL,
    type: 'GET',
    headers : {
      'Authorization' : ppJwt
    },
    dataType: 'json',
    success: function(response) {
        pinpoll_init_selectbox(response);
        pinpoll_draw_lineChart(response, 0);
    },
    error: function(jqXHR, textStatus, errorThrown) {
      console.info('Something went wrong!' + textStatus);
    }
  });
});

/**
 * Initiate Selectbox
 * Description: Load Top 5 Polls in Selectbox
 * @param  {array} chartData votes and views
 */
function pinpoll_init_selectbox(chartData) {

  var selectBox = document.getElementById('pp-select-box');

  for (var i = 0; i < chartData.polls.length; i++) {
    var option = document.createElement("option");
    option.text = chartData.polls[i].question;
    option.value = i;
    selectBox.add(option);
  }
}

/**
 * Draw Line Chart
 * Description: Initiate chart, fill with data and draw chart
 * @param  {array} chartData [votes and views]
 * @param  {int} id        [pollId]
 */
function pinpoll_draw_lineChart(chartData, id) {

  //if current chart exist, destroy it
  if(ppTopFiveChart !== null) {
    ppTopFiveChart.destroy();
  }

  //refresh chart data with new stats
  if(chartData !== null) {
    data = chartData;
  }

  var labels = [];

  //initiate labels for x and y axes in line chart
  for (var j = 0; j < data.hours.length; j++) {
     labels[j] = (data.hours[j] > 9) ? data.hours[j] : "0" + data.hours[j];
     labels[j] = labels[j] + ":00";
  }
  try {
    //initiate chart
    var ctx = document.getElementById("pp-top-five").getContext("2d");
    ppTopFiveChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
              {
                label: "Votes",
                backgroundColor: "rgba(28,132,198,0.6)",
                borderColor: "#1c84c6",
                borderCapStyle: 'butt',
                borderWidth: 1,
                pointBorderColor: "#1c84c6",
                pointBackgroundColor: "rgba(28,132,198,0.6)",
                pointHoverRadius: 4,
                pointHoverBackgroundColor: "rgba(28,132,198,1)",
                pointHoverBorderColor: "rgba(28,132,198,1)",
                pointHoverBorderWidth: 2,
                fillColor: '#1c84c6',
                data: data.polls[id].votesByHour
              },
              {
                label: "Views",
                backgroundColor: "rgba(220,220,220,0.6)",
                borderColor: "#CCC",
                borderCapStyle: 'butt',
                borderWidth: 1,
                pointBorderColor: "#CCC",
                pointBackgroundColor: "rgba(220,220,220,0.6)",
                pointHoverRadius: 4,
                pointHoverBackgroundColor: "rgba(220,220,220,1)",
                pointHoverBorderColor: "rgba(220,220,220,1)",
                pointHoverBorderWidth: 2,
                fillColor: '#CCC',
                data: data.polls[id].viewsByHour
              },
          ]
        },
        options: {
          scales: {
              xAxes: [{
                  time: {
                      unit: 'hour'
                  }
              }],
              yAxes: [{
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
                var valueVotes = data.datasets[0].data[tooltipItem.index];
                var labelVotes = data.datasets[0].label;
                var valueViews = data.datasets[1].data[tooltipItem.index];
                var labelViews = data.datasets[1].label;
                return labelVotes + ': ' + valueVotes + ' ' + labelViews + ': ' + valueViews;
              }
            }
          }
      }
    });
  } catch(e) {}
}

/**
 * Listener: Selectbox #pp-select-box
 * Description: Change data of chart if the poll in selectebox has changed
 */
jQuery('#pp-select-box').on('change', function() {
  pinpoll_draw_lineChart(null, this.value);
});

/**
 * Count-Animation for votes
 * Description: Animate alle numbers (total votes 24hours, total votes 30days);
 *              Count from 0 to TOTAL_VOTES
 */
jQuery('.pp-count-votes').each(function () {
  var votes = jQuery(this);
  jQuery({ Counter: 0 }).animate({ Counter: votes.text() }, {
    duration: 2000,
    easing: 'swing',
    step: function () {
      votes.text(addThousandDots(Math.ceil(this.Counter)));
    },
    complete: function() {
      votes.text(addThousandDots(this.Counter));
    }
  });
});

/**
 * Slide Animation
 * Description: Slide-Down Animations for feedback
 */
jQuery(document).ready(function() {
  if(ppShowFeedback == 'show') {
    if(jQuery("#pp-feedback:first").is(":hidden")) {
        jQuery('#pp-feedback').slideDown(900);
    }
  }
});

/**
 * Helper Method for Count Animation
 * Description: Add thousand dots to total votes 24 hours and total votes 30 days
 * @param {int} number number (total votes)
 */
function addThousandDots(number) {
  return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

/**
 * Slide Animation
 * Description: Slide-Down Animations for feedback formular, if user clicks
 *              on the "Heart"-Button or dismisses the message
 */
jQuery('#pp-love').click(function() {
  if(jQuery('#pp-check').is(':hidden') && jQuery('#pp-check-hate').is(':hidden')) {
    jQuery('#pp-love').hide();
    jQuery('#pp-hate').hide();
    jQuery('#pp-check').show();
    jQuery('#pp-check-hate').show();
    jQuery('#pp-feedback-header-text').html(ppReviewText);
  }

  if(!jQuery('#pp-improve-text').is(':hidden')) {
    jQuery('#pp-improve-text').slideUp(900);
  }
});

/**
 * Slide Animation
 * Description: Slide-Down Animations for feedback formular, if user clicks
 *              on the "Heart"-Button or dismisses the message
 */
jQuery('#pp-hate').click(function() {
  jQuery('#pp-improve-text').slideDown(900);
});

/**
 * Slide Animation
 * Description: Slide-Down Animations for feedback formular, if user clicks
 *              on the "Heart"-Button or dismisses the message
 */
jQuery('#pp-check').click(function() {
  if(jQuery('#pp-review-text').is(":hidden")) {
    if(!jQuery('#pp-make-a-wish-text').is(":hidden")) {
      jQuery('#pp-make-a-wish-text').slideUp(900);
      jQuery('#pp-review-text').delay(1000).slideDown(900);
    } else {
      jQuery('#pp-review-text').slideDown(900);
    }
  }
});

/**
 * Slide Animation
 * Description: Slide-Down Animations for feedback formular, if user clicks
 *              on the "Heart"-Button or dismisses the message
 */
jQuery('#pp-check-hate').click(function() {
  if(jQuery('#pp-make-a-wish-text').is(":hidden")) {
    if(!jQuery('#pp-review-text').is(":hidden")) {
      jQuery('#pp-review-text').slideUp(900);
      jQuery('#pp-make-a-wish-text').delay(1000).slideDown(900);
    } else {
      jQuery('#pp-make-a-wish-text').slideDown(900);
    }

  }
});

/**
 * Listener: LinkButton #pp-plugin-review
 * Description: If linkbutton is clicked, user gets redirected to the plugin
 *              review page of wordpress. Then the curren date will be saved to
 *              the wp_options table that the feedback formular appears only
 *              once a month.
 */
jQuery('#pp-plugin-review').click(function() {
  jQuery.post(ppFileUrl, { 'ppFeedbackDate' : 'now' }, function(data) {
    jQuery('#pp-feedback').slideUp(900);
    jQuery('#pp-review-text').slideUp(900);
    console.info('Sucesscully stored Feedbackdate');
  }).fail(function() {
    console.info('Storing Feedbackdate ' + now + 'failed!');
  });
});


/**
 * Listener: Button #pp-no-feedback
 * Description: If linkbutton is clicked, user gets redirected to the plugin
 *              review page of wordpress. Then the curren date will be saved to
 *              the wp_options table that the feedback formular appears only
 *              once a month.
 */
jQuery('#pp-no-feedback').click(function() {

  var now = new Date();

  jQuery.post(ppFileUrl, { 'ppFeedbackDate' : now }, function(data) {
    jQuery('#pp-feedback').slideUp(900);
    jQuery('#pp-improve-text').slideUp(900);
    console.info('Sucesscully stored Feedbackdate: ' + now);
  }).fail(function() {
    console.info('Storing Feedbackdate ' + now + 'failed!');
  });

});

/**
 * Listener: Button #pp-no-feedback-wish
 * Description: If user clicks "Not now, thanks!" button after clicking
 *              "Heart"-Button -> "Cross"-Button; Method calls helper function
 *              pinpoll_send_feedback to send feedback via api to pinpoll with
 *              a message from the textarea.
 */
jQuery('#pp-no-feedback-wish').click(function() {
  var bodyData = {
    'review' : 'good',
    'weebly_clicked' : '',
    'app' : 'Wordpress',
    'message' : ''
  };

  pinpoll_send_feedback(bodyData);
});

/**
 * Listener: Button #pp-send-feedback
 * Description: If user clicks "Send feedback" button after clicking
 *              "Cross"-Button; method calls helper function
 *              pinpoll_send_feedback to send feedback via api to pinpoll with
 *              a message from the textarea.
 */
jQuery('#pp-send-feedback').click(function() {

  var message = document.getElementById('pp-feedback-hate-text').value;
  var bodyData = {
    'review' : 'bad',
    'weebly_clicked' : '',
    'app' : 'Wordpress',
    'message' : message
  };

  pinpoll_send_feedback(bodyData);

});

/**
 * Listener: Button #pp-send-feedback
 * Description: If user clicks "Send feedback" button after clicking
 *              "Heart"-Button -> "Cross"-Button; method calls helper function
 *              pinpoll_send_feedback to send feedback via api to pinpoll with
 *              a message from the textarea.
 */
jQuery('#pp-send-feedback-wish').click(function() {

  var message = document.getElementById('pp-feedback-wish-text').value;
  var bodyData = {
    'review' : 'good',
    'weebly_clicked' : '',
    'app' : 'Wordpress',
    'message' : message
  };

  pinpoll_send_feedback(bodyData);

});

/**
 * Send Feedback
 * Description: Send feedback given from the different possibilites to pinpoll
 *              via API with jQuery post.
 * @param  {array} bodyData request body
 */
function pinpoll_send_feedback(bodyData) {
  jQuery.ajax({
    url: ppFeedbackBaseURL,
    type: 'POST',
    headers : {
      'Authorization' : ppJwt
    },
    data : bodyData,
    dataType: 'json',
    success: function(response) {
      jQuery('#pp-feedback').slideUp(900);

      var now = new Date();

      jQuery.post(ppFileUrl, { 'ppFeedbackDate' : now }, function(data) {
        console.info('Sucesscully stored Feedbackdate: ' + now);
      }).fail(function() {
        console.info('Storing Feedbackdate ' + now + 'failed!');
      });
    },
    error: function(jqXHR, textStatus, errorThrown) {
      console.info('Something went wrong!' + textStatus);
    }
  });
}
