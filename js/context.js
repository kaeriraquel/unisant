$(document).ready(function() {
  $('body').on('contextmenu', function() {
    document.getElementById("rmenu").className = "hide2";
    let x = event.clientX;
    let y = event.clientY;
    let element = document.elementFromPoint(x,y);

    if($(element).attr("type")!="text"){
      document.getElementById("rmenu").className = "show2 list-group";
      document.getElementById("rmenu").style.top = event.pageY-25 + 'px';
      document.getElementById("rmenu").style.left = event.pageX + 'px';
      window.event.returnValue = false;
    }

    console.log(element);
  });
});

// this is from another SO post...
$(document).bind("click", function(event) {
  document.getElementById("rmenu").className = "hide2";
});
