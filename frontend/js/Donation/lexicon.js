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
	jQuery('label[for="en__field_supporter_NOT_TAGGED_310"]').html(window.NRO_PROPERTIES[NRO].gift.type.recurring);
	jQuery('label[for="en__field_supporter_NOT_TAGGED_311"]').html(window.NRO_PROPERTIES[NRO].gift.type.single);
  
	return donationLexicon;
}
