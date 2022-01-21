// floatrx: SingleNotify popup
function ShowSingleNotify(args){
  var
    CSS  = args.css ? args.css : {},
    kind = args.kind ? args.kind : false,
    delay= args.delay ? args.delay : 5000

  if(kind){
    var palette = []
        palette['default'] = '#4E5359'
        palette['danger']  = '#B8312F'
        palette['info']    = '#2C82C9'
        palette['success'] = '#41A85F'
        palette['warning'] = '#FE9700'
        palette['wait']    = '#5F7C8A'
    CSS.background = palette[kind]
  }//if kind

  var
  text  = args.text ? "<p class='notify-message animated'>"+args.text+"</p>" : "",
  title = args.title ? "<div class='notify-title'> <i class='fa fa-info-circle'></i> "+args.title+"</div>" : "",
  el    = $('<div id=single-notify class='+kind+'-notify>')
            .html(title + text) //add content
            .css(CSS); //stylish
  if (!title && !text) return
  $('#single-notify').remove()//remove previous popup
  el.appendTo('body').stop().delay(delay).fadeOut()


 }

function ShowWarnNotify(msg,title){
  title = !title ? '<i class="fa fa-exclamation-triangle"></i> WARNING!' : title;
  ShowSingleNotify({
      title: title,
      text: msg,
      kind: 'warning',
    }
  );
  return false;
}
function ShowNotify(msg){
  ShowSingleNotify({
      text: msg,
      kind: 'default',
    }
  );
}
function ShowInfoNotify(msg){
  ShowSingleNotify({
      text: msg,
      kind: 'info',
    }
  );
  return false;
}
function ShowErrorNotify(msg,title){
  $('#single-notify').remove()
  title = !title ? '<span class="woops"></span> ¡Ups!' : title;
  ShowSingleNotify({
        title: title,
        text: msg,
        kind: 'danger',
        delay: 3000
      }
  );
  return false;
}
function ShowSuccessNotify(msg,title){
  $('#single-notify').remove()
  title = !title ? '¡Bien hecho!' : title;
  ShowSingleNotify({
        title: title,
        text: msg,
        kind: 'success',
        delay: 3000
      }
  );
  return true;
}
function ShowWaitNotify(msg){
  $('#single-notify').remove()
  ShowSingleNotify({
        title: '<i class="fa fa-spinner fa-spin"></i> Espere por favor ...',
        text: msg,
        kind: 'wait',
      }
  );
}
function ShowWaitNotifyTime(msg){
  $('#single-notify').remove()
  ShowSingleNotify({
        title: '<i class="fa fa-spinner fa-spin"></i> Espere por favor ...',
        text: msg,
        kind: 'wait',
        delay:360000
      }
  );
}
function updNotify(new_text){
  if (new_text == undefined) new_text = '';
  $('#single-notify > .notify-message').addClass('flash').html(new_text);
}
function hideNotify(){
  $('#single-notify').stop().fadeOut('slow')
}
// Templates
function LoginErr(){
  ShowErrNotify("Sorry, unrecognized <b>Email</b> or <b>Password</b>.");
  btnCheckoutEnable();
}
// testing feature

 $(document).ready(function(){
  $('#test_wait').click(function(){
      ShowWaitNotify('Wait for something');
  });
  $('#test1').click(function(){
    ShowSingleNotify({
      title:'You are notified!', //title can be NaN
      text:'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
      kind:'danger', //~ info,success,danger or empty
      delay:7000, //~ 5000 by default
      // ~ also you can rewrite default css rules
      css:{'animation-duration':'.35s',
           'animation-delay':'.1s',
           'transform-origin':'50% 50%',
          } //custom pop animation

    })
  })
})
