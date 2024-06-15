
navbar = document.querySelector('.header .flex .navbar');

document.querySelector('#menu-btn').onclick = () =>{
   navbar.classList.toggle('active');
   profile.classList.remove('active');
}

profile = document.querySelector('.header .flex .profile');

document.querySelector('#user-btn').onclick = () =>{
   profile.classList.toggle('active');
   navbar.classList.remove('active');
}

window.onscroll = () =>{
   navbar.classList.remove('active');
   profile.classList.remove('active');
}

function loader(){
   document.querySelector('.loader').style.display = 'none';
}

function fadeOut(){
   setInterval(loader, 2000);
}

window.onload = fadeOut;

document.querySelectorAll('input[type="number"]').forEach(numberInput => {
   numberInput.oninput = () =>{
      if(numberInput.value.length > numberInput.maxLength) numberInput.value = numberInput.value.slice(0, numberInput.maxLength);
   };
});


// $(document).ready(function() {
//    $("#checkout-form").submit(function(event) {
//       event.preventDefault(); // Prevent the default form submission
      
//       // Serialize the form data into a format that can be sent via AJAX
//       var formData = $(this).serialize();
      
//       // Make an AJAX POST request
//       $.ajax({
//          type: "POST",
//          url: "admin/confirm_orders.php",
//          data: formData,
//          success: function(response) {
//             // Display the response from confirm_orders.php in a suitable location on your page
//             // For example, you can replace the contents of a div with the response:
//             $("#response-container").html(response);
//          },
//          error: function(xhr, status, error) {
//             // Handle errors if any
//             console.error(xhr.responseText);
//          }
//       });
//    });
// });




