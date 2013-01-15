function twitterCallback2(twitters) {
  var statusHTML = [];
  for (var i=0; i<twitters.length; i++){
    var username = twitters[i].user.screen_name;
    var status = twitters[i].text.replace(/((https?|s?ftp|ssh)\:\/\/[^"\s\<\>]*[^.,;'">\:\s\<\>\)\]\!])/g, function(url) {
      return '<a href="'+url+'">'+url+'</a>';
    }).replace(/\B([@|#][_a-z0-9]+)/ig, function(reply) {
      return  '<a href="//twitter.com/'+reply.substring(0)+'">'+reply.substring(0)+'</a>';
    });
    statusHTML.push('<li><span>'+status+'</span> <a href="//twitter.com/'+username+'/statuses/'+twitters[i].id_str+'">'+relative_time(twitters[i].created_at)+'</a></li>');
  }
  document.getElementById('member_tweets').innerHTML = statusHTML.join('');
}

function relative_time(time_value) {
  var values = time_value.split(" ");
  time_value = values[1] + " " + values[2] + ", " + values[5] + " " + values[3];
  var parsed_date = Date.parse(time_value);
  var relative_to = (arguments.length > 1) ? arguments[1] : new Date();
  var delta = parseInt((relative_to.getTime() - parsed_date) / 1000);
  delta = delta + (relative_to.getTimezoneOffset() * 60);

  if (delta < 60) {
    return 'alldeles nyss';
  } else if(delta < 120) {
    return '1 minut sedan';
  } else if(delta < (60*60)) {
    return (parseInt(delta / 60)).toString() + ' minuter sedan';
  } else if(delta < (120*60)) {
    return '1 timma sedan';
  } else if(delta < (24*60*60)) {
    return '' + (parseInt(delta / 3600)).toString() + ' timmar sedan';
  } else if(delta < (48*60*60)) {
    return '1 dag sedan';
  } else {
    return (parseInt(delta / 86400)).toString() + ' dagar sedan';
  }
}