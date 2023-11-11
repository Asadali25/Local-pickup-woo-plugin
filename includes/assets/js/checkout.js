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
const CityCodeInput = document.getElementById("city_code_input");
targetList.addEventListener("click", function(event) {
  let clickedListItem = event.target.closest("li");
  if (clickedListItem) {
    let cityID = clickedListItem.querySelector(".cityId");
    sendCityID(cityID.textContent);
    const citySpan = clickedListItem.querySelector(".list-city");

    targetInput.value = citySpan.textContent;
    CityCodeInput.value = cityID.textContent;
  }
});






// Disable Divs on checkout
 function DisableHTML(isenabled){
  if(isenabled){
    $("#l2option_1").css('display', 'none');
  } else{
    $('#l2option_1').fadeTo('fast',1);
  }}
  DisableHTML(true);










// disable model until radio is checked
const radioBox1 = document.getElementById("radio1");
const Div1 = document.getElementById("l2option_1");
const Div2 = document.getElementById("l2option_2");
const Div3 = document.getElementById("l2option_3");

const radioBox2 = document.getElementById("radio2");
const radioBox3 = document.getElementById("radio3");
const ModalOpen = document.getElementById("openModal");
const option2_address = document.getElementById("billingAddress");
radioBox1.addEventListener("change", function() {
  if (radioBox1.checked) {
    ModalOpen.removeAttribute("disabled");
    option2_address.setAttribute("disabled", "true");
    Div1.style.borderColor= '#2B2B2B';
    Div2.style.borderColor= '#F5F5F5';
    Div3.style.borderColor= '#F5F5F5';
  }
});



radioBox2.addEventListener("change", function() {
  if (radioBox2.checked) {
    ModalOpen.setAttribute("disabled", "true");
    option2_address.removeAttribute("disabled");
    Div1.style.borderColor= '#F5F5F5';
    Div2.style.borderColor= '#2B2B2B';
    Div3.style.borderColor= '#F5F5F5';
    

  }
});




radioBox3.addEventListener("change", function() {
  if (radioBox3.checked) {
    ModalOpen.setAttribute("disabled", "true");
    option2_address.setAttribute("disabled", "true");
    Div1.style.borderColor= '#F5F5F5';
    Div2.style.borderColor= '#F5F5F5';
    Div3.style.borderColor= '#2B2B2B';
    


  }
});

// Input validation For Name And Contact Field 
function setupInputValidation(inputField, validationRegex) {
  inputField.addEventListener('input', function () {
      // Get the input value
      let inputValue = inputField.value;

      // Use the provided regular expression to check the input
      let isValid = validationRegex.test(inputValue);

      // Add or remove the 'invalid' class based on validation result
      if (!isValid) {
          inputField.classList.add('invalid');
      } else {
          inputField.classList.remove('invalid');
      }
  });
}

// Usage for phone validation
const PhoneField = document.getElementById('billing_phone');
setupInputValidation(PhoneField, /^\d+$/);

// Usage for name validation
const NameField = document.getElementById('billing_first_name');
setupInputValidation(NameField, /^[A-Za-z]+$/);





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
        DisableHTML(true);
        $("#levelid2").css('display', 'block');
        if(!jQuery.isEmptyObject(pickup_data)){
          DisableHTML(false);
          yadex_modal(pickup_data);

        }
    }
});

}});
