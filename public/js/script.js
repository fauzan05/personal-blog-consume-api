
// const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
// const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
//     return new bootstrap.Tooltip(tooltipTriggerEl);
// });
// let listPostButton = document.querySelectorAll('.list-post')
//   listPostButton.forEach((button, index) => {
//     button.addEventListener("click", () => {
//       console.log("hai");
//     })
//   })


document.addEventListener("DOMContentLoaded", () => {
  
  const pageButtons = document.querySelectorAll(".admin-page-button");
  pageButtons.forEach((button, index) => {
    button.addEventListener("click", () => {
      // console.log(`Klik pada halaman ${index + 1}`);
      Livewire.dispatch('admin-current-page', { data: index + 1 });
    })
  });

 
 
  

  // // Tag Option Button
  // const optionTagButton = document.getElementById('option-tag-button');
  // const saveTag = document.querySelectorAll('.save-tag');
  // const deleteTag = document.querySelectorAll('.delete-tag');
  // optionTagButton.addEventListener("click", () => {
   
  //   console.log("diklik");
  //   deleteTag.forEach((button, index) => {
  //     new bootstrap.Tooltip(button);
  //   })
  //   // saveTag.forEach((button, index) => {
  //   // new bootstrap.Tooltip(button);
  //   // })
  // })
  // console.log(getCookie("token"));
  const logoutButton = document.getElementById('logout-button')
  if (logoutButton) {
    new bootstrap.Tooltip(logoutButton); // Inisialisasi Logout Tooltip
  }

  let body = document.querySelector("body"),
    darkMode = body.querySelector('#dark-mode-switch')

  darkMode.addEventListener("click", () => {
    Livewire.dispatch('dark-mode');
    body.classList.toggle("dark")
  })
  // console.log("Token cookie" + document.cookie);
})
