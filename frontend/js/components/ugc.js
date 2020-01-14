/**
 * funcionality to feed fake input text
 *
 */
export default function() {
  document.querySelectorAll('.js-ugc_cover').forEach(input => {
    input.addEventListener('change', e => {
      
      let ugcInputFakeLabel = document.querySelector('.ugc_input_file_selected');

      if (ugcInputFakeLabel) {
        ugcInputFakeLabel.innerHTML = e.target.files[0].name;
      }     
    });
  });
}
