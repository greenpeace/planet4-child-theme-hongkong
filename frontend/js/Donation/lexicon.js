export default function() {

	// console.log(NRO);
	
	// console.log(window.NRO_PROPERTIES[NRO]);
	
	// console.log(window.NRO_PROPERTIES[NRO].form.labels.country);
	
	// 
	let donationLexicon = {};
	donationLexicon.countryLabel = (window.NRO_PROPERTIES[NRO].form.labels.country) ? (window.NRO_PROPERTIES[NRO].form.labels.country) : 'Select Country';
	donationLexicon.cityLabel = (window.NRO_PROPERTIES[NRO].form.labels.city) ? (window.NRO_PROPERTIES[NRO].form.labels.city) : 'Select City';
	donationLexicon.regionLabel = (window.NRO_PROPERTIES[NRO].form.labels.region) ? (window.NRO_PROPERTIES[NRO].form.labels.region) : 'Select Region';
  
	// set default fields
	jQuery('label[for="en__field_supporter_firstName"]').html(window.NRO_PROPERTIES[NRO].form.labels.first_name);
	jQuery('label[for="en__field_supporter_lastName"]').html(window.NRO_PROPERTIES[NRO].form.labels.last_name);
	jQuery('label[for="en__field_supporter_emailAddress"]').html(window.NRO_PROPERTIES[NRO].form.labels.email);
	jQuery('label[for="en__field_supporter_phoneNumber"]').html(window.NRO_PROPERTIES[NRO].form.labels.phone_number);
	jQuery('label[for="en__field_supporter_NOT_TAGGED_6"]').html(window.NRO_PROPERTIES[NRO].form.labels.dob);
	jQuery('label[for="en__field_supporter_address1"]').html(window.NRO_PROPERTIES[NRO].form.labels.address);
	jQuery('label[for="en__field_supporter_NOT_TAGGED_33"]').html(window.NRO_PROPERTIES[NRO].form.labels.ddc_recruiter_id);
	jQuery('label[for="en__field_transaction_donationAmt"]').html(window.NRO_PROPERTIES[NRO].form.others.other_amt);
	jQuery('label[for="en__field_supporter_NOT_TAGGED_311"]').html(window.NRO_PROPERTIES[NRO].gift.type.recurring);
	jQuery('label[for="en__field_supporter_NOT_TAGGED_310"]').html(window.NRO_PROPERTIES[NRO].gift.type.single);

	jQuery('label[for="en__field_transaction_ccnumber"]').html(window.NRO_PROPERTIES[NRO].form.labels.card_number);
	jQuery('label[for="en__field_transaction_ccexpire"]').html(window.NRO_PROPERTIES[NRO].form.labels.expiry);
	jQuery('label[for="en__field_transaction_ccvv"]').html(window.NRO_PROPERTIES[NRO].form.labels.cvv);

	jQuery('.js-step-amount').html(window.NRO_PROPERTIES[NRO].form.others.amount);
	jQuery('.js-step-details').html(window.NRO_PROPERTIES[NRO].form.others.details);
	jQuery('.js-step-payment').html(window.NRO_PROPERTIES[NRO].form.others.payment);

	jQuery('.cvv-text').append('<h3>' + window.NRO_PROPERTIES[NRO].form.others.what_is_cvv + '</h3>');

	if (window.NRO_PROPERTIES[NRO].form.others.cvv_explain) {
		jQuery( window.NRO_PROPERTIES[NRO].form.others.cvv_explain ).each(function( index ) {
			jQuery('.cvv-text').append('<p>' + window.NRO_PROPERTIES[NRO].form.others.cvv_explain[index] + '</p>');
		});	
		// const cvv_explain_details = window.NRO_PROPERTIES[NRO].form.others.cvv_explain.split(',');
		// cvv_explain_details.forEach
	}

	jQuery('#en__field_transaction_donationAmt').parent().prepend('<span class="donation__amount__currency">'+window.NRO_PROPERTIES[NRO].currency+'</span>');

	const footerText = '<p class="secondary-links">' + window.NRO_PROPERTIES[NRO].footer.copyright + ' | ' + window.NRO_PROPERTIES[NRO].footer.note + '</p>';
	jQuery('.js-footer-dynamic').html(footerText);

	let footerLinks = jQuery('<ul/>');
	footerLinks.addClass('secondary-links');

	jQuery( window.NRO_PROPERTIES[NRO].footer.links ).each(function( index ) {
		footerLinks.append('<li><a href="' + window.NRO_PROPERTIES[NRO].footer.links[index].link + '">' + window.NRO_PROPERTIES[NRO].footer.links[index].text + '</a></li>');
	});
	jQuery('.js-footer-dynamic').append(footerLinks);
	

	// jQuery('.js-privacy-cookies').html(window.NRO_PROPERTIES[NRO].footer.others.payment);
	// jQuery('.js-copyright-cookies').html(window.NRO_PROPERTIES[NRO].form.others.payment);

  
	return donationLexicon;
}
