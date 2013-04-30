

mw.site = {
  hvideo:function(id){
    var id = id || 'eajCiD0ha2s';
    var frame = '<iframe width="880" height="455" src="http://www.youtube.com/embed/'+id+'?rel=0&autoplay=1&wmode=transparent&vq=large" frameborder="0" allowfullscreen></iframe>';
    return frame;
  }
}


$(document).ready(function(){

    mw.$("#home-video").click(function(){
      if(this.getElementsByTagName('iframe').length === 0){
        this.innerHTML = mw.site.hvideo();
      }
    });

});





$(window).load(function(){


   // Simple way to enable the 'placeholder' attribute for browsers that doesn't support it
   if('placeholder' in document.createElement('input') === false){
       mw.$("[placeholder]").each(function(){
          var el = $(this), p = el.attr("placeholder");
          el.val() == '' ? el.val(p) : '' ;
          el.bind('focus', function(e){ el.val() == p ? el.val('') : ''; });
          el.bind('blur', function(e){ el.val() == '' ? el.val(p) : '';});
       });
   }


   /* Fixed shop cart */

   if(typeof _shop === 'boolean'){
      var header = document.getElementById('header');
      $(window).bind("scroll", function(){
          var cart = mw.$(".mw-cart-small", header)[0];
          var cart_module = mw.tools.firstParentWithClass(cart, 'module');
          if($(window).scrollTop() > $(header).outerHeight()){
            $(cart_module).addClass("mw-cart-small-fixed");
          }
          else{
             $(cart_module).removeClass("mw-cart-small-fixed");
          }
      });
  }
 });