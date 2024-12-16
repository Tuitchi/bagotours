$(document).ready(function () {
  const countries = [
    "Afghanistan",
    "Albania",
    "Algeria",
    "Andorra",
    "Angola",
    "Antigua and Barbuda",
    "Argentina",
    "Armenia",
    "Australia",
    "Austria",
    "Azerbaijan",
    "Bahamas",
    "Bahrain",
    "Bangladesh",
    "Barbados",
    "Belarus",
    "Belgium",
    "Belize",
    "Benin",
    "Bhutan",
    "Bolivia",
    "Bosnia and Herzegovina",
    "Botswana",
    "Brazil",
    "Brunei",
    "Bulgaria",
    "Burkina Faso",
    "Burundi",
    "Cabo Verde",
    "Cambodia",
    "Cameroon",
    "Canada",
    "Central African Republic",
    "Chad",
    "Chile",
    "China",
    "Colombia",
    "Comoros",
    "Congo (Congo-Brazzaville)",
    "Costa Rica",
    "Croatia",
    "Cuba",
    "Cyprus",
    "Czech Republic",
    "Democratic Republic of the Congo",
    "Denmark",
    "Djibouti",
    "Dominica",
    "Dominican Republic",
    "Ecuador",
    "Egypt",
    "El Salvador",
    "Equatorial Guinea",
    "Eritrea",
    "Estonia",
    "Eswatini",
    "Ethiopia",
    "Fiji",
    "Finland",
    "France",
    "Gabon",
    "Gambia",
    "Georgia",
    "Germany",
    "Ghana",
    "Greece",
    "Grenada",
    "Guatemala",
    "Guinea",
    "Guinea-Bissau",
    "Guyana",
    "Haiti",
    "Honduras",
    "Hungary",
    "Iceland",
    "India",
    "Indonesia",
    "Iran",
    "Iraq",
    "Ireland",
    "Israel",
    "Italy",
    "Jamaica",
    "Japan",
    "Jordan",
    "Kazakhstan",
    "Kenya",
    "Kiribati",
    "Korea (North)",
    "Korea (South)",
    "Kuwait",
    "Kyrgyzstan",
    "Laos",
    "Latvia",
    "Lebanon",
    "Lesotho",
    "Liberia",
    "Libya",
    "Liechtenstein",
    "Lithuania",
    "Luxembourg",
    "Madagascar",
    "Malawi",
    "Malaysia",
    "Maldives",
    "Mali",
    "Malta",
    "Marshall Islands",
    "Mauritania",
    "Mauritius",
    "Mexico",
    "Micronesia",
    "Moldova",
    "Monaco",
    "Mongolia",
    "Montenegro",
    "Morocco",
    "Mozambique",
    "Myanmar",
    "Namibia",
    "Nauru",
    "Nepal",
    "Netherlands",
    "New Zealand",
    "Nicaragua",
    "Niger",
    "Nigeria",
    "North Macedonia",
    "Norway",
    "Oman",
    "Pakistan",
    "Palau",
    "Panama",
    "Papua New Guinea",
    "Paraguay",
    "Peru",
    "Philippines",
    "Poland",
    "Portugal",
    "Qatar",
    "Romania",
    "Russia",
    "Rwanda",
    "Saint Kitts and Nevis",
    "Saint Lucia",
    "Saint Vincent and the Grenadines",
    "Samoa",
    "San Marino",
    "Sao Tome and Principe",
    "Saudi Arabia",
    "Senegal",
    "Serbia",
    "Seychelles",
    "Sierra Leone",
    "Singapore",
    "Slovakia",
    "Slovenia",
    "Solomon Islands",
    "Somalia",
    "South Africa",
    "South Sudan",
    "Spain",
    "Sri Lanka",
    "Sudan",
    "Suriname",
    "Sweden",
    "Switzerland",
    "Syria",
    "Taiwan",
    "Tajikistan",
    "Tanzania",
    "Thailand",
    "Timor-Leste",
    "Togo",
    "Tonga",
    "Trinidad and Tobago",
    "Tunisia",
    "Turkey",
    "Turkmenistan",
    "Tuvalu",
    "Uganda",
    "Ukraine",
    "United Arab Emirates",
    "United Kingdom",
    "United States of America",
    "Uruguay",
    "Uzbekistan",
    "Vanuatu",
    "Vatican City",
    "Venezuela",
    "Vietnam",
    "Yemen",
    "Zambia",
    "Zimbabwe",
  ];

  // Dropdown elements
  const $countryDropdown = $("#country");
  const $provinceDropdown = $("#province");
  const $cityDropdown = $("#city");

  // Populate country dropdown
  $.each(countries, function (_, country) {
    $countryDropdown.append(`<option value="${country}">${country}</option>`);
  });

  // Handle country change event
  $countryDropdown.on("change", function () {
    const selectedCountry = $(this).val();

    if (selectedCountry === "Philippines") {
      $provinceDropdown.prop("disabled", false);
      $cityDropdown.prop("disabled", true);

      // Fetch provinces
      $.ajax({
        url: "php/getProvinces.php",
        method: "GET",
        dataType: "json",
        success: function (provinces) {
          $(".input-group.province").css("display", "block");
          $provinceDropdown.html(
            '<option value="" selected disabled>Select Province</option>'
          );
          $.each(provinces, function (_, province) {
            $provinceDropdown.append(
              `<option value="${province.name}" data-code="${province.code}">${province.name}</option>`
            );
          });
        },
        error: function (xhr, status, error) {
          console.error("Error fetching provinces:", error);
        },
      });
    } else {
      $provinceDropdown
        .prop("disabled", true)
        .html('<option value="" selected disabled>Select Province</option>');
      $cityDropdown
        .prop("disabled", true)
        .html(
          '<option value="" selected disabled>Select City/Municipality</option>'
        );
    }
  });

  // Handle province change event
  $provinceDropdown.on("change", function () {
    const provinceId = $(this).find(":selected").data("code");

    if (provinceId) {
      $cityDropdown.prop("disabled", false);

      // Fetch cities
      $.ajax({
        url: "php/getCities.php",
        method: "GET",
        data: { provinceId: provinceId },
        dataType: "json",
        success: function (cities) {
          $(".input-group.city").css("display", "block");
          $cityDropdown.html(
            '<option value="" selected disabled>Select City/Municipality</option>'
          );
          $.each(cities, function (_, city) {
            $cityDropdown.append(
              `<option value="${city.name}">${city.name}</option>`
            );
          });
        },
        error: function (xhr, status, error) {
          const errorMessage = xhr.responseText || error;
          Toast.fire({
            icon: "error",
            title: `Error: ${errorMessage}`,
          });
        },
      });
    } else {
      $cityDropdown
        .prop("disabled", true)
        .html(
          '<option value="" selected disabled>Select City/Municipality</option>'
        );
    }
  });
});
