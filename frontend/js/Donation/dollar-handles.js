/**
 * Functionality for the "dollar handle" donation form.
 *
 */
export default function() {

  // first check if we are using dollar handles, exit otherway
  // console.log(window.NRO_PROPERTIES[NRO].main.home[campaignName].options.recurring); 
  if ( 1 !== window.NRO_PROPERTIES[NRO].main.home[campaignName].dollarHandles ) return;

  // const stats = document.createElement('div');
  // stats.classList.add('signatures');
  // stats.innerHTML = document.querySelector('#petition-source').innerHTML;

  // start creating dollar handles options
  const dollaHandles = document.createElement('div');
  dollaHandles.classList.add('dollarHandlesContainer');
  // dollaHandles.innerHTML = '<div class="dollar-handles dollar-handles__once"></div>';
  let onceOptions = document.createElement('div');
  onceOptions.classList.add('dollar-handles', 'dollar-handles__once');
  let recurringOptions = document.createElement('div');
  recurringOptions.classList.add('dollar-handles', 'dollar-handles__recurring', 'is-active');

  let checkedInfo = '';
  let startParagraphOption = '';
  (window.NRO_PROPERTIES[NRO].main.home[campaignName].options.single).forEach(function (item, index) {    
    //onceOptions += '<div class="dollar-handle"><input type="radio" id="b' + item.amt + '" name="dollar-handle" value="' + item.amt + '" data-paragraph="' + item.text + '" ' + checkedInfo + ' /><label for="b' + item.amt + '" style="background-image: url(' + item.image + ')"><span>' + window.NRO_PROPERTIES[NRO].currency + ' '  + item.amt + '</span></label></div>';
    let dollarRadioOption = jQuery('<div class="dollar-handle"><input type="radio" id="b' + item.amt + '" name="dollar-handle" value="' + item.amt + '" data-paragraph="' + item.text + '" ' + checkedInfo + ' /><label for="b' + item.amt + '" style="background-image: url(' + item.image + ')"><span>' + window.NRO_PROPERTIES[NRO].currency + ' '  + item.amt + '</span></label></div>');
    dollarRadioOption.appendTo(onceOptions);
  });

  (window.NRO_PROPERTIES[NRO].main.home[campaignName].options.recurring).forEach(function (item, index) {
    if ( index === 1 ) {
      startParagraphOption = item.text;
      checkedInfo = 'checked';
      jQuery('input[name="transaction.donationAmt"]').val(item.amt);
    } else {
      checkedInfo = '';
    }
    
    let dollarRadioOption = jQuery('<div class="dollar-handle"><input type="radio" id="b' + item.amt + '" name="dollar-handle" value="' + item.amt + '" data-paragraph="' + item.text + '" ' + checkedInfo + ' /><label for="b' + item.amt + '" style="background-image: url(' + item.image + ')"><span>' + window.NRO_PROPERTIES[NRO].currency + ' '  + item.amt + '</span></label></div>');
    dollarRadioOption.appendTo(recurringOptions);

    // recurringOptions += '<div class="dollar-handle"><input type="radio" id="b' + item.amt + '" name="dollar-handle" value="' + item.amt + '" data-paragraph="' + item.text + '" ' + checkedInfo + ' /><label for="b' + item.amt + '" style="background-image: url(' + item.image + ')"><span>' + window.NRO_PROPERTIES[NRO].currency + ' '  + item.amt + '</span></label></div>';    
  });


  // dollaHandles.innerHTML = '<div class="dollar-handles dollar-handles__once">' + onceOptions + '</div><div class="dollar-handles dollar-handles__recurring is-active">' + recurringOptions + '</div><p class="caption paragraph-handles__once"></p><p class="caption paragraph-handles__recurring is-active">' + startParagraphOption + '</p>';

  jQuery(onceOptions).appendTo(dollaHandles);
  jQuery(recurringOptions).appendTo(dollaHandles);
  let paragraphInfo = '<p class="caption paragraph-handles__once"></p><p class="caption paragraph-handles__recurring is-active">' + startParagraphOption + '</p>';
  jQuery(paragraphInfo).appendTo(dollaHandles);

  // insert all the options before the amount field

  const amountFormField = document.querySelector('.en__field--donationAmt');
  if ( amountFormField ) {
    amountFormField.parentNode.insertBefore(dollaHandles, amountFormField);
  }

  // Update paragraph content, and
  // Clear free amount value when I select a dollar handle
  //dollaHandles.querySelector('input').addEventListener('change', e => {
  jQuery(document).on('change', '.dollar-handle input', function(e){
    const form = e.target.form;
    const group = e.target.closest('.dollar-handles');
    const paragraph = group.classList.contains('dollar-handles__once')
      ? form.querySelector('.paragraph-handles__once')
      : form.querySelector('.paragraph-handles__recurring');
    const paragraphOther = group.classList.contains(
      'dollar-handles__recurring'
    )
      ? form.querySelector('.paragraph-handles__once')
      : form.querySelector('.paragraph-handles__recurring');
    const freeAmount = form['transaction.donationAmt'];
    paragraph.innerHTML = this.dataset.paragraph;
    paragraphOther.textContent = '';
    freeAmount.value = this.value;
  });

  // Clear checked dollar handle if I insert a free amount
  document.querySelector('#en__field_transaction_donationAmt').addEventListener('change', e => {
    const form = e.target.form;
    const checkedHandles = form['dollar-handle'];
    if ( checkedHandles ) {
      Array.from(checkedHandles).forEach(radio => {
        radio.checked = false;
      });
    }    
  });
}
