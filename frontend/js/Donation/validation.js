import validate from 'validate.js'; // lightweight validation library: http://validatejs.org/

const requiredMessage = window.NRO_PROPERTIES[NRO].validation.required;
const invalidMessage = 'Please check the format of this field';
const countryMessage = 'Should be one of "HK", "TW", "KR"';

// const invalidePhone = window.NRO_PROPERTIES[NRO].validation.format_phone;

const required = {
  allowEmpty: false,
  message: requiredMessage,
};

const amountConstraintsRecurring = {
  ['en__field--donationAmt']: {
    presence: required,
    numericality: {
      onlyInteger: true,
      strict: true,
      greaterThanOrEqualTo: parseInt(
        window.NRO_PROPERTIES[NRO].min_amt_recurring
      ),
      message: window.NRO_PROPERTIES[NRO].validation.min_amt_recurring,
    },
  },
};

const amountConstraintsSingle = {
  ['en__field--donationAmt']: {
    presence: required,
    numericality: {
      onlyInteger: true,
      strict: true,
      greaterThanOrEqualTo: parseInt(window.NRO_PROPERTIES[NRO].min_amt_single),
      message: window.NRO_PROPERTIES[NRO].validation.min_amt_single,
    },
  },
};

const dataConstraints = {
  ['en__field--firstName']: {
    presence: required,
    length: { maximum: 255 },
  },
  ['en__field--lastName']: {
    presence: required,
    length: { maximum: 255 },
  },
  ['en__field--emailAddress']: {
    presence: required,
    length: { maximum: 255 },
    email: {
      message: window.NRO_PROPERTIES[NRO].validation.format_email,
    },
  },
  ['en__field--phoneNumber']: {
    // numericality: {
    // onlyInteger: true,
    // },
    // length: { is: 8 },
      format: {
        pattern: regexPhone,
        message: regexPhoneMessage
      }
    // the validation should happen with the masking, so no other rule required here
  },
  ['en__field--country']: {
    presence: required,
    // // inclusion: {
    // // within: ['HK', 'TW', 'KR'],
    // // message: countryMessage,
    // // },
  },
  ['en__field--region']: {
    presence: required,
  },
  ['en__field--city']: {
    presence: required,
  },
  ['en__field--address1']: {
    presence: required,
    length: { maximum: 255 },
  },
  // ['en__field--NOT_TAGGED_33']: {
  //   presence: false,
  //   format: {
  //     pattern: window.NRO_PROPERTIES[NRO].regex.national_id,
  //     message: window.NRO_PROPERTIES[NRO].validation.format_id
  //   },
  // },
};

const paymentConstraints = {
  // the format should be taken care by the masking
  ['en__field--ccnumber']: {
    presence: required,
    // format: {
    //   pattern: /^(34|37|4|5[1-5]).*$/,
    //   message: function(value, attribute, validatorOptions, attributes, globalOptions) {
    //     return validate.format("^%{num} " + window.NRO_PROPERTIES[NRO].invalid_card, {
    //       num: value
    //     });
    //   }
    // },
    // length: function(value, attributes, attributeName, options, constraints) {
    //   if (value) {
    //     let valueStrip = value.replace(/\s/g, '');
    //     console.log(valueStrip);
    //     // Amex
    //     if ((/^(34|37).*$/).test(valueStrip)) return {is: 15};
    //     // Visa, Mastercard
    //     if ((/^(4|5[1-5]).*$/).test(valueStrip)) return {is: 16};
    //   }
    //   // Unknown card, don't validate length
    //   return false;
    // }
  },
  ['en__field--ccexpire']: {
    presence: required,
    // ccexpire: function(value, options, key, attributes) {
    //   let dateInfo = value.split("/");
    //   let today = new Date();
    //   let cardDate = new Date();
    //   cardDate.setFullYear(dateInfo[1], dateInfo[0], 1);
    //   if (cardDate < today) {
    //     return window.NRO_PROPERTIES[NRO].validation.invalid_mm_yy;
    //  }
    // }
  },
  ['en__field--ccvv']: {
    presence: required,
  },
};

const allConstraints = Object.assign(
  {},
  amountConstraintsRecurring,
  amountConstraintsSingle,
  dataConstraints,
  paymentConstraints
);

function prepareFormForValidation(form) {
  for (const name in allConstraints) {
    if (allConstraints.hasOwnProperty(name)) {
      // const options = allConstraints[name];
      let input = form.querySelector('.' + name + ' input');
      if (!input) input = form.querySelector('.' + name + ' select');
      if (!input) continue;
      input.setAttribute('data-gpea-constraint-name', name);
      const feedback = document.createElement('span');
      feedback.classList.add('invalid-feedback');
      input.parentNode.insertBefore(feedback, input.nextSibling);
    }
  }
}

export default function(form, donationLexicon) {
  prepareFormForValidation(form);  

  const constrained = form.querySelectorAll(
    'input[data-gpea-constraint-name], select[data-gpea-constraint-name]'
  );

  Array.from(constrained).forEach(input => {
    input.addEventListener('change', e => {

      // update phone rules for dynamic regex
      if (input.dataset.gpeaConstraintName) {
        allConstraints['en__field--phoneNumber'] = {
          format: {
            pattern: regexPhone,
            message: regexPhoneMessage
          }
        }
      }

      const invalid = validate.single(
        input.value,
        allConstraints[input.dataset.gpeaConstraintName]
      );
      
      if (invalid && invalid.length) {
        input.parentNode.querySelector('.invalid-feedback').textContent =
          invalid[0];
      } else {
        input.classList.remove('is-invalid');
      }
    });
  });

  const validateBlock = function(block, step) {
    const constraints =
      step === 'amountRecurring'
        ? amountConstraintsRecurring
        : step === 'amountSingle'
        ? amountConstraintsSingle
        : step === 'payment'
        ? paymentConstraints
        : dataConstraints;
    const constrained = block.querySelectorAll(
      'input[data-gpea-constraint-name], select[data-gpea-constraint-name]'
    );
    const values = {};

    Array.from(constrained).forEach(input => {
      values[input.dataset.gpeaConstraintName] = input.value;
    });
    
    // update phone regex since it's dynamic
    constraints['en__field--phoneNumber'] = {
      format: {
        pattern: regexPhone,
        message: regexPhoneMessage
      }
    }

    const invalid = validate(values, constraints, { fullMessages: false });

    if (invalid === undefined) return true;

    for (const name in invalid) {
      if (invalid.hasOwnProperty(name)) {
        const message = invalid[name][0];
        const selector = '[data-gpea-constraint-name="' + name + '"]';
        const input = block.querySelector(selector);
        const feedback = block.querySelector(
          '[data-gpea-constraint-name="' + name + '"] + .invalid-feedback'
        );
        if (input) input.classList.add('is-invalid');
        feedback.textContent = message;
      }
    }

    return false;
  };

  return validateBlock;
}
