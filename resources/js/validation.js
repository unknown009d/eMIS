/* 
    validateInput : 
    It is a function that will run in every text field
    which will make sure that there isn't any sql attacks     

    If website is slow and it lags then change the 
    validation to particular section rather than full page 
*/

const validateInput = (input) => {
  let regex = /[^a-zA-Z0-9_\-,\\?\/()@#|=:;.&% ]/g;
  input.value = input.value.replace(regex, "").replace(/\s{2,}/g, " ");
};

const validation = document.querySelector("#validation");
validation.addEventListener("input", (e) => {
  if (
    e.target.matches(
      "input[type='text'], input[type='number'], input[type='email'], input[type='tel'], textarea"
    )
  ) {
    validateInput(e.target);
  }
});
