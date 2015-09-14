/**
 * Created by guopj on 2015/8/21.
 */
$(function(){

    //console.log( $("#sub").click());
   $("#sub").click(function(){
        var data =$('comment_form').serialize();
   });

    $('.widget').click(function(){
        alert('点点');
    });
});
