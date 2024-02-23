<?php
/**
 * Schema data types, sourced from https://schema.org/docs/tree.jsonld
 *
 * @info Stiofan has a PHP script to build this in his localhost. (build_schema_array.php)
 */
function geodir_data_schemas() {
	return array(
		'Brand' => 'Brand',
		'BroadcastChannel' => 'BroadcastChannel',
		'RadioChannel' => '- RadioChannel',
		'AMRadioChannel' => '- - AMRadioChannel',
		'FMRadioChannel' => '- - FMRadioChannel',
		'TelevisionChannel' => '- TelevisionChannel',
		'JobPosting' => 'JobPosting',
		'Observation' => 'Observation',
		'Airline' => 'Airline',
		'Consortium' => 'Consortium',
		'Corporation' => 'Corporation',
		'EducationalOrganization' => 'EducationalOrganization',
		'CollegeOrUniversity' => '- CollegeOrUniversity',
		'ElementarySchool' => '- ElementarySchool',
		'HighSchool' => '- HighSchool',
		'MiddleSchool' => '- MiddleSchool',
		'Preschool' => '- Preschool',
		'School' => '- School',
		'FundingScheme' => 'FundingScheme',
		'GovernmentOrganization' => 'GovernmentOrganization',
		'LibrarySystem' => 'LibrarySystem',
		'LocalBusiness' => 'LocalBusiness',
		'AnimalShelter' => '- AnimalShelter',
		'ArchiveOrganization' => '- ArchiveOrganization',
		'AutomotiveBusiness' => '- AutomotiveBusiness',
		'AutoBodyShop' => '- - AutoBodyShop',
		'AutoDealer' => '- - AutoDealer',
		'AutoPartsStore' => '- - AutoPartsStore',
		'AutoRental' => '- - AutoRental',
		'AutoRepair' => '- - AutoRepair',
		'AutoWash' => '- - AutoWash',
		'GasStation' => '- - GasStation',
		'MotorcycleDealer' => '- - MotorcycleDealer',
		'MotorcycleRepair' => '- - MotorcycleRepair',
		'ChildCare' => '- ChildCare',
		'Dentist' => '- Dentist',
		'DryCleaningOrLaundry' => '- DryCleaningOrLaundry',
		'EmergencyService' => '- EmergencyService',
		'FireStation' => '- - FireStation',
		'Hospital' => '- - Hospital',
		'PoliceStation' => '- - PoliceStation',
		'EmploymentAgency' => '- EmploymentAgency',
		'EntertainmentBusiness' => '- EntertainmentBusiness',
		'AdultEntertainment' => '- - AdultEntertainment',
		'AmusementPark' => '- - AmusementPark',
		'ArtGallery' => '- - ArtGallery',
		'Casino' => '- - Casino',
		'ComedyClub' => '- - ComedyClub',
		'MovieTheater' => '- - MovieTheater',
		'NightClub' => '- - NightClub',
		'FinancialService' => '- FinancialService',
		'AccountingService' => '- - AccountingService',
		'AutomatedTeller' => '- - AutomatedTeller',
		'BankOrCreditUnion' => '- - BankOrCreditUnion',
		'InsuranceAgency' => '- - InsuranceAgency',
		'FoodEstablishment' => '- FoodEstablishment',
		'Bakery' => '- - Bakery',
		'BarOrPub' => '- - BarOrPub',
		'Brewery' => '- - Brewery',
		'CafeOrCoffeeShop' => '- - CafeOrCoffeeShop',
		'Distillery' => '- - Distillery',
		'FastFoodRestaurant' => '- - FastFoodRestaurant',
		'IceCreamShop' => '- - IceCreamShop',
		'Restaurant' => '- - Restaurant',
		'Winery' => '- - Winery',
		'GovernmentOffice' => '- GovernmentOffice',
		'PostOffice' => '- - PostOffice',
		'HealthAndBeautyBusiness' => '- HealthAndBeautyBusiness',
		'BeautySalon' => '- - BeautySalon',
		'DaySpa' => '- - DaySpa',
		'HairSalon' => '- - HairSalon',
		'HealthClub' => '- - HealthClub',
		'NailSalon' => '- - NailSalon',
		'TattooParlor' => '- - TattooParlor',
		'HomeAndConstructionBusiness' => '- HomeAndConstructionBusiness',
		'Electrician' => '- - Electrician',
		'GeneralContractor' => '- - GeneralContractor',
		'HVACBusiness' => '- - HVACBusiness',
		'HousePainter' => '- - HousePainter',
		'Locksmith' => '- - Locksmith',
		'MovingCompany' => '- - MovingCompany',
		'Plumber' => '- - Plumber',
		'RoofingContractor' => '- - RoofingContractor',
		'InternetCafe' => '- InternetCafe',
		'LegalService' => '- LegalService',
		'Attorney' => '- - Attorney',
		'Notary' => '- - Notary',
		'Library' => '- Library',
		'LodgingBusiness' => '- LodgingBusiness',
		'BedAndBreakfast' => '- - BedAndBreakfast',
		'Campground' => '- - Campground',
		'Hostel' => '- - Hostel',
		'Hotel' => '- - Hotel',
		'Motel' => '- - Motel',
		'Resort' => '- - Resort',
		'SkiResort' => '- - - SkiResort',
		'VacationRental' => '- - VacationRental',
		'MedicalBusiness' => '- MedicalBusiness',
		'CommunityHealth' => '- - CommunityHealth',
		'Dentist' => '- - Dentist',
		'Dermatology' => '- - Dermatology',
		'DietNutrition' => '- - DietNutrition',
		'Emergency' => '- - Emergency',
		'Geriatric' => '- - Geriatric',
		'Gynecologic' => '- - Gynecologic',
		'MedicalClinic' => '- - MedicalClinic',
		'CovidTestingFacility' => '- - - CovidTestingFacility',
		'Midwifery' => '- - Midwifery',
		'Nursing' => '- - Nursing',
		'Obstetric' => '- - Obstetric',
		'Oncologic' => '- - Oncologic',
		'Optician' => '- - Optician',
		'Optometric' => '- - Optometric',
		'Otolaryngologic' => '- - Otolaryngologic',
		'Pediatric' => '- - Pediatric',
		'Pharmacy' => '- - Pharmacy',
		'Physiotherapy' => '- - Physiotherapy',
		'PlasticSurgery' => '- - PlasticSurgery',
		'Podiatric' => '- - Podiatric',
		'PrimaryCare' => '- - PrimaryCare',
		'Psychiatric' => '- - Psychiatric',
		'PublicHealth' => '- - PublicHealth',
		'ProfessionalService' => '- ProfessionalService',
		'RadioStation' => '- RadioStation',
		'RealEstateAgent' => '- RealEstateAgent',
		'RecyclingCenter' => '- RecyclingCenter',
		'SelfStorage' => '- SelfStorage',
		'ShoppingCenter' => '- ShoppingCenter',
		'SportsActivityLocation' => '- SportsActivityLocation',
		'BowlingAlley' => '- - BowlingAlley',
		'ExerciseGym' => '- - ExerciseGym',
		'GolfCourse' => '- - GolfCourse',
		'HealthClub' => '- - HealthClub',
		'PublicSwimmingPool' => '- - PublicSwimmingPool',
		'SkiResort' => '- - SkiResort',
		'SportsClub' => '- - SportsClub',
		'StadiumOrArena' => '- - StadiumOrArena',
		'TennisComplex' => '- - TennisComplex',
		'Store' => '- Store',
		'AutoPartsStore' => '- - AutoPartsStore',
		'BikeStore' => '- - BikeStore',
		'BookStore' => '- - BookStore',
		'ClothingStore' => '- - ClothingStore',
		'ComputerStore' => '- - ComputerStore',
		'ConvenienceStore' => '- - ConvenienceStore',
		'DepartmentStore' => '- - DepartmentStore',
		'ElectronicsStore' => '- - ElectronicsStore',
		'Florist' => '- - Florist',
		'FurnitureStore' => '- - FurnitureStore',
		'GardenStore' => '- - GardenStore',
		'GroceryStore' => '- - GroceryStore',
		'HardwareStore' => '- - HardwareStore',
		'HobbyShop' => '- - HobbyShop',
		'HomeGoodsStore' => '- - HomeGoodsStore',
		'JewelryStore' => '- - JewelryStore',
		'LiquorStore' => '- - LiquorStore',
		'MensClothingStore' => '- - MensClothingStore',
		'MobilePhoneStore' => '- - MobilePhoneStore',
		'MovieRentalStore' => '- - MovieRentalStore',
		'MusicStore' => '- - MusicStore',
		'OfficeEquipmentStore' => '- - OfficeEquipmentStore',
		'OutletStore' => '- - OutletStore',
		'PawnShop' => '- - PawnShop',
		'PetStore' => '- - PetStore',
		'ShoeStore' => '- - ShoeStore',
		'SportingGoodsStore' => '- - SportingGoodsStore',
		'TireShop' => '- - TireShop',
		'ToyStore' => '- - ToyStore',
		'WholesaleStore' => '- - WholesaleStore',
		'TelevisionStation' => '- TelevisionStation',
		'TouristInformationCenter' => '- TouristInformationCenter',
		'TravelAgency' => '- TravelAgency',
		'MedicalOrganization' => 'MedicalOrganization',
		'Dentist' => '- Dentist',
		'DiagnosticLab' => '- DiagnosticLab',
		'Hospital' => '- Hospital',
		'MedicalClinic' => '- MedicalClinic',
		'Pharmacy' => '- Pharmacy',
		'Physician' => '- Physician',
		'IndividualPhysician' => '- - IndividualPhysician',
		'PhysiciansOffice' => '- - PhysiciansOffice',
		'VeterinaryCare' => '- VeterinaryCare',
		'NGO' => 'NGO',
		'NewsMediaOrganization' => 'NewsMediaOrganization',
		'OnlineBusiness' => 'OnlineBusiness',
		'OnlineStore' => '- OnlineStore',
		'PerformingGroup' => 'PerformingGroup',
		'DanceGroup' => '- DanceGroup',
		'MusicGroup' => '- MusicGroup',
		'TheaterGroup' => '- TheaterGroup',
		'PoliticalParty' => 'PoliticalParty',
		'Project' => 'Project',
		'FundingAgency' => '- FundingAgency',
		'ResearchProject' => '- ResearchProject',
		'ResearchOrganization' => 'ResearchOrganization',
		'SearchRescueOrganization' => 'SearchRescueOrganization',
		'SportsOrganization' => 'SportsOrganization',
		'SportsTeam' => '- SportsTeam',
		'WorkersUnion' => 'WorkersUnion',
		'Accommodation' => 'Accommodation',
		'Apartment' => '- Apartment',
		'CampingPitch' => '- CampingPitch',
		'House' => '- House',
		'SingleFamilyResidence' => '- - SingleFamilyResidence',
		'Room' => '- Room',
		'HotelRoom' => '- - HotelRoom',
		'MeetingRoom' => '- - MeetingRoom',
		'Suite' => '- Suite',
		'AdministrativeArea' => 'AdministrativeArea',
		'City' => '- City',
		'Country' => '- Country',
		'SchoolDistrict' => '- SchoolDistrict',
		'State' => '- State',
		'CivicStructure' => 'CivicStructure',
		'Airport' => '- Airport',
		'Aquarium' => '- Aquarium',
		'Beach' => '- Beach',
		'BoatTerminal' => '- BoatTerminal',
		'Bridge' => '- Bridge',
		'BusStation' => '- BusStation',
		'BusStop' => '- BusStop',
		'Campground' => '- Campground',
		'Cemetery' => '- Cemetery',
		'Crematorium' => '- Crematorium',
		'EducationalOrganization' => '- EducationalOrganization',
		'EventVenue' => '- EventVenue',
		'FireStation' => '- FireStation',
		'GovernmentBuilding' => '- GovernmentBuilding',
		'CityHall' => '- - CityHall',
		'Courthouse' => '- - Courthouse',
		'DefenceEstablishment' => '- - DefenceEstablishment',
		'Embassy' => '- - Embassy',
		'LegislativeBuilding' => '- - LegislativeBuilding',
		'Hospital' => '- Hospital',
		'MovieTheater' => '- MovieTheater',
		'Museum' => '- Museum',
		'MusicVenue' => '- MusicVenue',
		'Park' => '- Park',
		'ParkingFacility' => '- ParkingFacility',
		'PerformingArtsTheater' => '- PerformingArtsTheater',
		'PlaceOfWorship' => '- PlaceOfWorship',
		'BuddhistTemple' => '- - BuddhistTemple',
		'Church' => '- - Church',
		'CatholicChurch' => '- - - CatholicChurch',
		'HinduTemple' => '- - HinduTemple',
		'Mosque' => '- - Mosque',
		'Synagogue' => '- - Synagogue',
		'Playground' => '- Playground',
		'PoliceStation' => '- PoliceStation',
		'PublicToilet' => '- PublicToilet',
		'RVPark' => '- RVPark',
		'StadiumOrArena' => '- StadiumOrArena',
		'SubwayStation' => '- SubwayStation',
		'TaxiStand' => '- TaxiStand',
		'TrainStation' => '- TrainStation',
		'Zoo' => '- Zoo',
		'Landform' => 'Landform',
		'BodyOfWater' => '- BodyOfWater',
		'Canal' => '- - Canal',
		'LakeBodyOfWater' => '- - LakeBodyOfWater',
		'OceanBodyOfWater' => '- - OceanBodyOfWater',
		'Pond' => '- - Pond',
		'Reservoir' => '- - Reservoir',
		'RiverBodyOfWater' => '- - RiverBodyOfWater',
		'SeaBodyOfWater' => '- - SeaBodyOfWater',
		'Waterfall' => '- - Waterfall',
		'Continent' => '- Continent',
		'Mountain' => '- Mountain',
		'Volcano' => '- Volcano',
		'LandmarksOrHistoricalBuildings' => 'LandmarksOrHistoricalBuildings',
		'LocalBusiness' => 'LocalBusiness',
		'Residence' => 'Residence',
		'ApartmentComplex' => '- ApartmentComplex',
		'GatedResidenceCommunity' => '- GatedResidenceCommunity',
		'TouristAttraction' => 'TouristAttraction',
		'TouristDestination' => 'TouristDestination',
		'DietarySupplement' => 'DietarySupplement',
		'Drug' => 'Drug',
		'IndividualProduct' => 'IndividualProduct',
		'ProductCollection' => 'ProductCollection',
		'ProductGroup' => 'ProductGroup',
		'ProductModel' => 'ProductModel',
		'SomeProducts' => 'SomeProducts',
		'Vehicle' => 'Vehicle',
		'BusOrCoach' => '- BusOrCoach',
		'Car' => '- Car',
		'Motorcycle' => '- Motorcycle',
		'MotorizedBicycle' => '- MotorizedBicycle',
		'Person' => 'Person',
	);
}
