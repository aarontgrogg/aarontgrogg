<?php
echo '<pre class="hidden atg">';
$array = array(
'Hotels etc'=>['accommodation','tla','tdy','lodging'],
'Tax Preparation'=>['accountant'],
'Airport Shuttle'=>['airport transfer'],
'Pets - Veterinary Clinics'=>['animal hospital'],
'Electronics'=>['appliances','white goods','tv','computer'],
'Home Decor'=>['bedding','home accessories','household','wallpaper'],
'Bicycle Sales & Service'=>['bike'],
'Financial Planning'=>['broker','accountant','savings','retirment plan','portfolio'],
'Printing Service & Graphic Design'=>['business cards','web design','homepage','advertising'],
'Auto Rental'=>['car hire'],
'Auto Cleaning & Detailing'=>['car wash','detailer'],
'Medical - Cardiology'=>['cardiovascular','heart'],
'Party Service & Catering'=>['caterer','dj','disc jockey','event','wedding'],
'Medical - Pediatrics'=>['children','kids'],
'Medical - Alternative Medicine'=>['chinese medicine','homeopathy'],
'Medical - Chiropractors'=>['chiropractic'],
'Cleaning Services'=>['cleaner'],
'Gift Shops & Specialty Stores'=>['clocks','souvenirs','christmas decoration','boutique'],
'Golf Courses & Golf Equipment'=>['clubs'],
'Optical Shops, Optometry'=>['contacts','glasses','lenses','frames'],
'Medical - Aesthetic & Plastic Surgery'=>['cosmetic surgery'],
'Medical - Psychotherapy'=>['counselling','psychology','counseller','therapy'],
'Banks'=>['credit union'],
'Medical - Dentists'=>['dental','orthodontist'],
'Hardware'=>['diy'],
'Pets - Sales & Supplies'=>['dog food','cat food','canine','feline'],
'Medical - Pharmacies'=>['drugstore'],
'Pharmacies'=>['drugstore'],
'Schools'=>['education'],
'Utilities'=>['electricity','water','gas','oil'],
'Jewelers & Jewelry - Watch Repair'=>['engagement ring','wedding ring','necklace'],
'Beauty - Health & Body Care'=>['facial','spa','wellness','manicure','pedicure','massage'],
'Personal Trainer'=>['fitness'],
'Donuts'=>['food'],
'Adoption'=>['fostering'],
'Arts, Crafts & Frames'=>['framing','prints','copy shop'],
'Medical - General Practioner'=>['gp','doctor'],
'Sports, Fitness & Dance'=>['gym','classes'],
'Beauty - Hair Care'=>['hair dresser','hair salon','barber'],
'Drugstore'=>['health products','beauty products'],
'Locksmiths & Keys'=>['keycutting'],
'Telephone Services & Stores'=>['landline'],
'Computer Sales & Service'=>['laptop'],
'Dry Cleaning'=>['launderette'],
'Legal Services'=>['lawyer','attorney'],
'Motorcycle - Sales & Accessories'=>['motorbike'],
'Preschool & Kindergarten'=>['nursery','child care','education'],
'Gardening Centers & Gardening Services'=>['nursery','landscaping','maintenance','horticulture'],
'Farmer\'s Market'=>['organic','vegetables','fruit','harvest'],
'Auto Paint & Body'=>['paintwork','body shop'],
'Pets - Boarding, Grooming & O…'=>['pet hotel','groomer'],
'Music - Classes, Instruments & Repair'=>['piano','guitare'],
'Abortion Alternative'=>['pregnancy'],
'Auto Recycling'=>['recovery'],
'Churches'=>['religious service'],
'Carpets & Tapestries'=>['rugs'],
'Swimming Pools'=>['sauna','aquatic'],
'Education'=>['schools','university','college','degree','diploma'],
'Medical - Dermatology'=>['skin'],
'Trophies & Engraving'=>['souvenir','plaques','awards'],
'Auto Parts'=>['spares'],
'Tanning'=>['sun bed'],
'Restaurants'=>['take out','take away','delivery'],
'Sports Gear & Supplies'=>['tennis','running','weights','gym'],
'Auto Towing & Salvage'=>['towing','recovery','recycling'],
'Travel - Tours'=>['trips'],
'Moving & Storage Services'=>['u-haul'],
'Tax Relief'=>['VAT'],
'Recreation & Fun'=>['zoo','circus','leisure','children','activity','outdoor']
);
//print_r($array);
foreach ($array as $key => $value) {
  foreach ($value as $k => $v) {
    echo $v;
  }
}
echo '</pre>';
?>