 jQuery(document).ready(function($){
    //  AJAX Function For Live search
     function fetchData(){
      var s = $("#urban_input").val();

      if (s == '') {
         $('#urban_dropdown').css('display', 'none');
      }
      $.post("/wp-content/plugins/Urbanboss_checkout/includes/urban_api.php", 
            {
              s:s
            },
            function(data, status){
                if (data != "not found") {
                  $('#urban_dropdown').css('display', 'block');
                  $('#urban_dropdown').html(data);
                }
            });
    }
    $('#urban_input').on('input', fetchData);
    $("body").on('click', () => {
      $('#urban_dropdown').css('display', 'none');
    });
    $('#urban_input').on('click', fetchData);

console.log('connected succesfully');


// City Dropdown LI selection
const targetList = document.getElementById("urban_dropdown");
const targetInput = document.getElementById("urban_input");
targetList.addEventListener("click", function(event) {
  if (event.target && event.target.tagName == "LI") {
    let cityID = event.target.querySelector(".cityId");
     sendCityID(cityID.textContent);
    const citySpan = event.target.querySelector(".list-city");

      targetInput.value = citySpan.textContent + ' - ' + cityID.textContent;
  }
});

// Change Text For level One Checkout
  let h3element = $('.woocommerce-billing-fields').find('h3');
  h3element.html('<svg width="21" height="21" viewBox="0 0 21 21" fill="none" class="lvl1" xmlns="http://www.w3.org/2000/svg"><path d="M8.32354 7.57V6.25L11.9235 5.35V16H10.6485V6.88L8.32354 7.57Z" fill="#2B2B2B"/><circle cx="10.5" cy="10.5" r="10" stroke="#2B2B2B"/></svg><h3 class="billing-lv1"> Введите ваши Данные</h3>');



// Disable Divs on checkout
 function DisableHTML(isenabled){
  $("#l2option_1").find("*").prop("disabled", isenabled);
  if(isenabled){
  $('#l2option_1').fadeTo('slow',.6);
  } else{
    $('#l2option_1').fadeTo('slow',1);
  }}
  DisableHTML(true);
console.log('executed')



function sendCityID(city_id){
// Make an AJAX request
$.ajax({
    type: 'POST',
    url: '/wp-admin/admin-ajax.php',
    data: {
        action: 'my_ajax_action', // The AJAX action defined in PHP
        php_var: city_id // Your JavaScript variable
    },
    success: function(response) {
        // Handle the response from the server
        const pickup_data = JSON.parse(response);
        DisableHTML(true);;
        if(!jQuery.isEmptyObject(pickup_data)){
          DisableHTML(false);
          yadex_modal(pickup_data);
        }
    }
});

}



});
