(function(){

	var en = {};

	en.dependencies = [];

	en.altLists = [];

	en.validators = [{"componentId":450541,"type":"REQ","format":"","errorMessage":"payment_type 為必填"},{"componentId":450542,"type":"REQ","format":"","errorMessage":"card_expiration_date 為必填"},{"componentId":450543,"type":"REQ","format":"","errorMessage":"card_verification_value 為必填"},{"componentId":450544,"type":"REQ","format":"","errorMessage":"card_number 為必填"},{"componentId":450538,"type":"REQ","format":"","errorMessage":"recurring_frequency 為必填"},{"componentId":450539,"type":"REQ","format":"","errorMessage":"fr_rg_frequency 為必填"},{"componentId":450540,"type":"REQ","format":"","errorMessage":"gift_amount 為必填"},{"componentId":358083,"type":"REQ","format":"","errorMessage":"first_name 為必填"},{"componentId":358084,"type":"REQ","format":"","errorMessage":"email 為必填"},{"componentId":358085,"type":"REQ","format":"","errorMessage":"last_name 為必填"},{"componentId":358086,"type":"REQ","format":"","errorMessage":"phone_number 為必填"},{"componentId":358089,"type":"REQ","format":"","errorMessage":"region 為必填"},{"componentId":358090,"type":"REQ","format":"","errorMessage":"country 為必填"},{"componentId":358091,"type":"REQ","format":"","errorMessage":"city 為必填"},{"componentId":358092,"type":"REQ","format":"","errorMessage":"postcode 為必填"},{"componentId":358093,"type":"REQ","format":"","errorMessage":"address_1 為必填"}];

	en.alerts = [{"type":"MFE","content":"為必填"},{"type":"GPE","content":"捐款過程發生錯誤，請核對您的信用卡資料，然後再作嘗試。"},{"type":"GPE","content":"捐款過程發生錯誤，請核對您的信用卡資料，然後再作嘗試。"},{"type":"MFE","content":"為必填"}];

	en.premiumGifts = {};

	window.EngagingNetworks = en;

})()