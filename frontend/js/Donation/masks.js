import Cleave from 'cleave.js'; // simple masking library: https://nosir.github.io/cleave.js/

import './phone-type-formatter.hk-kr-tw.js';

export default function(form) {
  // These here select the <input> elements - change the classes if needed:
  const emailInput = form.querySelector('.en__field--emailAddress input');
  const phoneInput = form.querySelector('.en__field--phoneNumber input');
  const birthDateInput = form.querySelector('.en__field--NOT_TAGGED_6 input');
  const countryInput = form.querySelector('.en__field--country input');
  const ccNumberInput = form.querySelector('.en__field--ccnumber input');
  const ccExpInput = form.querySelector('.en__field--ccexpire input');
  const ccCvcInput = form.querySelector('.en__field--ccvv input');

  // Sometimes we assign placeholders, sometimes we change input types,
  // and sometimes we add a typing mask (Cleave)  --v

  if (emailInput) {
    emailInput.type = 'email';
  }

  // if (phoneInput) {
  //   if (window.NRO && window.NRO === 'TW') {
  //     phoneInput.placeholder = '0123456789';
  //     new Cleave(phoneInput, {
  //       phone: true,
  //       phoneRegionCode: 'TW',
  //     });
  //   } else if (window.NRO && window.NRO === 'HK') {
  //     phoneInput.placeholder = '23456789';
  //     new Cleave(phoneInput, {
  //       phone: true,
  //       phoneRegionCode: 'HK',
  //     });
  //   } else if (window.NRO && window.NRO === 'KR') {
  //     // phoneInput.placeholder = '?';
  //     new Cleave(phoneInput, {
  //       phone: true,
  //       phoneRegionCode: 'KR',
  //     });
  //   }
  // }

  if (birthDateInput) {
    birthDateInput.placeholder = 'dd/mm/yyyy';
    new Cleave(birthDateInput, {
      date: true,
      datePattern: ['d', 'm', 'Y'],
    });
  }

  if (countryInput) {
    countryInput.placeholder = 'HK';
    new Cleave(countryInput, {
      blocks: [2],
      uppercase: true,
    });
  }

  if (ccNumberInput) {
    ccNumberInput.placeholder = '0000 0000 0000 0000';
    new Cleave(ccNumberInput, {
      creditCard: true,
      onCreditCardTypeChanged: function(type) {
        // update UI ...
        // type = amex mastercard visa diners discover jcb dankort instapayment uatp mir unionPay
      },
    });
  }

  if (ccExpInput) {
    ccExpInput.placeholder = 'mm/yy';
    new Cleave(ccExpInput, {
      date: true,
      datePattern: ['m', 'y'],
    });
  }

  if (ccCvcInput) {
    new Cleave(ccCvcInput, {
      blocks: [4],
      numericOnly: true,
    });
  }
}
