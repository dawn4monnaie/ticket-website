<?php include("geoHash.php");

$ticketmaster_event_search_api_key = "uy2mh8pBbjq91kGF2qse3pSUWbw4oZAS";
$google_location_api_key = "AIzaSyBmVbs0kvYQsuD_Dpj8xgO5Wrpdg2AewPk";


if (isset($_POST["submit"])) {
    $lat = $_POST["lat"];
    $lgn = $_POST["lon"];
    # https://maps.googleapis.com/maps/api/geocode/json?address=   &key=YOUR_API_KEY
    if ($_POST["from"] == "location") {
        $location_name = join("+", explode(" ", $_POST["location"]));
        $location_url = "https://maps.googleapis.com/maps/api/geocode/json?address="
            . $location_name . "&key=" . $google_location_api_key;
        $location_response = json_decode(file_get_contents($location_url), true);
        $location_position = $location_response["results"][0]["geometry"]["location"];
        $lat = $location_position["lat"];
        $lgn = $location_position["lng"];
    }

    # https://app.ticketmaster.com/discovery/v2/events.json?apikey= YOUR_API_KEY & keyword=  &segmentId=KZFzniwnSyZfZ7v7nE &radius=10 &unit=miles &geoPoint=9q5cs
    #Category SegmentId
    #Music 			KZFzniwnSyZfZ7v7nJ
    #Sports 		KZFzniwnSyZfZ7v7nE
    #Arts & Theatre	KZFzniwnSyZfZ7v7na
    #Film 			KZFzniwnSyZfZ7v7nn
    #Miscellaneous 	KZFzniwnSyZfZ7v7n1
    $catergory2Id = array("Music" => "KZFzniwnSyZfZ7v7nJ", "Sports" => "KZFzniwnSyZfZ7v7nE", "Arts & Theatre" => "KZFzniwnSyZfZ7v7na", "Film" => "KZFzniwnSyZfZ7v7nn", "Miscellaneous" => "KZFzniwnSyZfZ7v7n1");
    $hscode = encode($lat, $lgn);
    $catergory = $_POST["category"];

    $nearbysearch_url = "https://app.ticketmaster.com/discovery/v2/events.json?apikey="
        . $ticketmaster_event_search_api_key
        . "&keyword=" . str_replace(" ", "+", $_POST["keyword"])
        . "&radius=" . $_POST["distance"]
        . "&unit=miles"
        . "&geoPoint=" . $hscode;

    if ($catergory !== "default") {
        $catergoryId = $catergory2Id[$catergory];
        $nearbysearch_url = $nearbysearch_url . "&segmentId=" . $catergoryId;
    }
    $nearbysearch_json = file_get_contents($nearbysearch_url);
    $nearbysearch_response = json_decode($nearbysearch_json, true);
    array_push($nearbysearch_response, $lat, $lgn);
    echo json_encode($nearbysearch_response);
    file_put_contents('log.txt', $nearbysearch_json);

    return;
}

#https://app.ticketmaster.com/discovery/v2/events/G5diZfkn0B-bh.json?apikey=
#https://app.ticketmaster.com/discovery/v2/venues.json?keyword=UCV&apikey=

if (isset($_POST["event_details"])) {
    $event_id = $_POST["event_id"];
    $event_details_url = "https://app.ticketmaster.com/discovery/v2/events/" . $event_id . ".json?apikey=" . $ticketmaster_event_search_api_key;
    $event_details_json = file_get_contents($event_details_url);
    $event_details_response = json_decode($event_details_json, true);
    echo $event_details_json;
    return;
}


if (isset($_POST["venue_info_details"])) {
    $keyword = $_POST["keyword"];
    $venue_details_url = "https://app.ticketmaster.com/discovery/v2/venues?apikey=" . $ticketmaster_event_search_api_key
        . "&keyword=" . str_replace(" ", "%20", $keyword);
    $venue_details_json = file_get_contents($venue_details_url);
    echo $venue_details_json;
    return;
}


if (isset($_POST["venue_photo_details"])) {
    $keyword = $_POST["keyword"];
    $venue_details_url = "https://app.ticketmaster.com/discovery/v2/venues?apikey=" . $ticketmaster_event_search_api_key
        . "&keyword=" . str_replace(" ", "%20", $keyword);
    $venue_details_json = file_get_contents($venue_details_url);
    echo $venue_details_json;

    return;
}



?>

<html>
<head>
    <style>
        body {
            text-align: center;
        }

        h1 {
            font-weight: normal;
            margin: 0;
        }
        h4 {
            margin: 0;
        }

        #main_border {
            border-style: solid;
            border-color: darkgray;
            text-align: center;
            margin: auto;
            width: 50%;
            background-color: rgb(250, 250, 250);
        }

        #main_form {
            text-align: center;
            position: relative;
            padding-left: 5%;
            padding-right: 5%;
            border: 1px;
        }
        #table td{
            vertical-align: bottom;
        }


        .search_table{
            width: 90%;
            border-style: solid;
            border-color: lightgray;
            border-collapse: collapse;
            margin: auto;
        }
        .search_table th, .search_table td {
            border-style: solid;
            border-color: lightgray;
            text-align: center;
        }

        .details_table{
            padding-left: 15%;
            padding-right: 5%;
            margin: auto;
        }

        a {
            color: black;
            text-decoration:none;
        }

        img.icon {
            width: 50px;
        }

        img.down_icon{
            width: 30px;
        }

        img.seatmap{
            width: 80%;
        }

        img.photos {
            max-width: 94%;
            padding: 3%;
        }

        .time{
            text-align: center;
            position: relative;
        }

        .map {
            width: 300px;
            height: 200px;
            position: absolute;
        }
        .mode {
            position: absolute;
        }

        .info_map {
            height: 200px;
            float: right;
            width: 50%;
            margin-right: 150px;
        }

        .info_mode {
            float: left;
            width: 20%;
            margin-left: 30px;
        }

        .travel_mode {
            background-color: rgb(240, 240, 240);
            border-width: 0;
            border-spacing: 0;
        }

        .travel_mode tr{
            border-width: 0;
        }

        .travel_mode td{
            height: 40px;
            width: 90px;
            border-width: 0;
            padding: 0;
            text-align: center;
        }

        .travel_mode td:hover{
            background-color: lightgray;
        }


        .placedetails_table_info{
            width: 70%;
            border-style: solid;
            border-color: lightgray;
            border-collapse: collapse;
            margin: auto;
        }
        .placedetails_table_info td{
            border-style: solid;
            border-color: lightgray;
            text-align: center;
        }

        .placedetails_table_photo {

            width: 45%;
            border-style: solid;
            border-color: lightgray;
            border-collapse: collapse;
            margin: auto;
        }

        .center_text_table {
            text-align: center;
        }



    </style>
</head>



<body onload="load()">

<div id="main_border">
    <h1> <i> Events Search </i></h1>
    <form id="main_form" action="" method="post" onsubmit="submit_form(this); return false">
        <hr />
        <table id="table">
            <tr>
                <td>
                    <label for="keyword"> <b> Keyword<span class="red_star">*<span> </b></label>
                    <input type="text" name="keyword" id="keyword" required="required" />
                    <br />
                    <label for="category"> <b> Category </b></label>
                    <select id="category" name="category">
                        <option value="default"> Default </option>
                        <option value="Music"> Music </option>
                        <option value="Sports"> Sports </option>
                        <option value="Arts & Theatre"> Arts & Theatre </option>
                        <option value="Film"> Film </option>
                        <option value="Miscellaneous"> Miscellaneous </option>
                    </select>
                    <br />
                    <label for="distance"> <b> Distance (miles)</b></label>
                    <input type="number" name="distance" id="distance" placeholder="10" /><b> from </b>
                </td>
                <td>
                    <input type="radio" name="from" id="here_radio" value="here" checked onclick="here_click()"/><label for="here">Here</label>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="radio" name="from" id="location_radio" value="location" onclick="location_click()"/><input type="text" name="location" id="location" placeholder="location" required="required" disabled />
                </td>
            </tr>
            <tr>
                <td id="table_button">
                    <input type="submit" value="Search" id="submit_button" disabled />
                    <input type="button" value="Clear" onclick="clear_all()" />
                </td>
            </tr>
        </table>
        <input type="hidden" id="lat" name="lat" />
        <input type="hidden" id="lon" name="lon" />
    </form>
</div>
</br>
<div id="response_content"></div>



<script type="text/javascript">
    var start_lat;
    var start_lng;

    var event_id;
    var keyword;


    function load() {
        let ip_api_location_Url = "http://ip-api.com/json";
        let xmlHttp =new XMLHttpRequest();
        xmlHttp.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                let response_json = JSON.parse(this.response);
                document.getElementById("lat").value = response_json.lat;
                document.getElementById("lon").value = response_json.lon;
                document.getElementById("submit_button").disabled = false;
            }
        };
        xmlHttp.open("GET", ip_api_location_Url, false);
        xmlHttp.send();
        //document.getElementById("lat").value = 34.0223519;
        //document.getElementById("lon").value = -118.285117;
        //document.getElementById("submit_button").disabled = false;

    }

    function here_click() {
        document.getElementById("location").disabled = true;
    }

    function location_click() {
        document.getElementById("location").disabled = false;
    }

    function submit_form() {

        let xmlHttp = new XMLHttpRequest();
        xmlHttp.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                let jsonObj;
                let search_table = "";
                try {
                    // console.log(this.responseText);
                    jsonObj = JSON.parse(this.responseText);
                } catch (e) {
                    search_table = "<p id='no_records'> No Records have been found </p>";
                    document.getElementById("response_content").innerHTML = search_table;
                }
                //console.log(jsonObj);

                try {

                    start_lat = jsonObj[0];
                    start_lng = jsonObj[1];

                    const results = jsonObj['_embedded']['events'];
                    //console.log(results);


                    search_table = "<table class='search_table'>";
                    search_table += "<tr><th>Date</th><th>Icon</th><th>Event</th><th>Genre</th><th>Venue</th></tr>";
                    for (let i = 0; i < results.length; ++ i) {
                        search_table += "<tr>";
                        search_table += "<td class='time'>";
                        if (results[i]["dates"]["start"].hasOwnProperty("localDate")) {
                            search_table += results[i]["dates"]["start"]["localDate"] + "<br>";
                        }
                        if (results[i]["dates"]["start"].hasOwnProperty("localTime")) {
                            search_table += results[i]["dates"]["start"]["localTime"];
                        }
                        search_table += "</td>";
                        search_table += "<td><img class='icon' src='" + results[i]["images"][0]['url'] + "' /></td>";

                        event_id = results[i]["id"];
                        keyword = results[i]['_embedded']['venues'][0]['name'];

                        search_table += "<td><a href=\"javascript:show_details('"
                            + results[i]["id"] + "','"
                            + results[i]['_embedded']['venues'][0]['name']
                            + "');\">" +
                            results[i]["name"] + "</a></td>";

                        if (results[i]['classifications'][0]['segment']['name'] !== 'undefined') {
                            search_table += "<td>" + results[i]['classifications'][0]['segment']['name'] + "</td>";
                        }
                        else {
                            search_table += "<td> N/\A</td>";
                        }

                        let latitude = results[i]['_embedded']['venues'][0]['location']['latitude'];
                        let longitude = results[i]['_embedded']['venues'][0]['location']['longitude'];

                        search_table += "<td><a href=\"javascript:init_map('"+ latitude  +"','"+  longitude +"','"+ i +"');\">" +
                            results[i]['_embedded']['venues'][0]['name'] + "</a>" +
                            "<div class='map' id='map" + i + "' style='display:none' ></div>" +
                            "<div class='mode' id='mode" + i + "' style='display:none' ></div></td>";

                        search_table += "</tr>";
                    }
                    search_table += "</table>";
                }
                catch (e) {
                    search_table = "<p id='no_records'> No Records have been found </p>";
                }
                document.getElementById("response_content").innerHTML = search_table;
            }
        };
        xmlHttp.open("POST", "", true);
        xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        let form_data = new FormData(document.getElementById("main_form"));
        let params = [];
        for (let pair of form_data.entries()) {
            //console.log(pair);
            if (pair[0] === "distance" && pair[1] === "") {
                pair[1] = 10;
            }
            params.push(
                encodeURIComponent(pair[0]) + '=' +
                encodeURIComponent(pair[1])
            );
        }
        xmlHttp.send("submit=&"+params.join('&'));
    }

    function show_details(event_id, keyword) {
        let xmlHttp = new XMLHttpRequest();
        xmlHttp.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {

                // console.log(this.responseText);
                let result = JSON.parse(this.responseText);
                // console.log(result);
                let details_table = "<h4>" + result["name"] + "</h4><br />";
                details_table += "<table class='details_table'>";
                try {
                    let url = result['seatmap']['staticUrl'];
                    details_table += "<tr><td></td>";
                    details_table += "<td rowspan=\'16\'><img class='seatmap' src='" + url + "'/></td>";
                    details_table += "<tr>";
                }catch(e){
                    console.log("seatmap missing");
                }

                if (result["dates"]["start"].hasOwnProperty("localDate") || result["dates"]["start"].hasOwnProperty("localTime")) {
                    details_table += "<tr><td><h4>Date</h4></td></tr>";
                    details_table += "<tr><td>";
                    if (result["dates"]["start"].hasOwnProperty("localDate")) {
                        details_table += result["dates"]["start"]["localDate"] + " ";
                    }
                    if (result["dates"]["start"].hasOwnProperty("localTime")) {
                        details_table += result["dates"]["start"]["localTime"];
                    }
                    details_table += "</td></tr>";
                }

                try{
                    let teams_group = result['_embedded']['attractions'];
                    // console.log(teams_group);
                    if (teams_group.length > 0) {
                        details_table += "<tr><td><h4>Artist / Team</h4></td></tr>";
                        let teams_group_name = [];
                        for(let i=0; i < teams_group.length; i++){
                            teams_group_name.push(
                            "<a href='" + teams_group[i]['url'] + "' target='_blank'>"+ teams_group[i]['name'] + " </a>")
                        }
                        details_table += "<tr><td>" + teams_group_name.join(" | ") + "</td></tr>";
                    }
                }catch(e){
                    console.log("artist / team missing");
                }

                try{
                    let venue = result['_embedded']['venues'][0]['name'];
                    details_table += "<tr><td><h4>Venue</h4></td></tr>";
                    details_table += "<tr><td>" + venue + "</td></tr>";
                }catch(e){
                    console.log("venues missing");
                }

                try{
                    let genres = result['classifications'][0];
                    let genres_group = [];
                    console.log(genres);
                    let types = ['subGenre', 'genre', 'segment', 'subType', 'type'];

                    for(let i=0; i < types.length; i++ ){
                        let name = genres[types[i]]['name'];
                        if (name !== 'undefined' &&  name !== 'Undefined') {
                            genres_group.push(name);
                        }
                    }
                    //console.log(genres_group);
                    details_table += "<tr><td><h4>Genres</h4></td></tr>";
                    details_table += "<tr><td>" + genres_group.join(" | ") + "</td></tr>";
                }catch(e){
                    console.log("genre missing");
                }

                try {
                    let prices = result['priceRanges'][0];
                    details_table += "<tr><td><h4>Prices</h4></td></tr>";
                    details_table += "<tr><td>" + prices['min'] + "-" + prices['max'] + "  " + prices['currency'] + "</td></tr>";
                }catch(e){
                    console.log("price missing");
                }

                try {
                    let status = result['dates']['status']['code'];
                    details_table += "<tr><td><h4>Ticket Status</h4></td></tr>";
                    details_table += "<tr><td>" + status + "</td></tr>";
                }catch(e){
                    console.log("selling status missing");
                }

                try {
                    let url = result['url'];
                    details_table += "<tr><td><h4>Buy the Ticket at:</h4></td></tr>";
                    details_table += "<tr><td><a href='" + url + "' target='_blank'>Ticketmaster</a></td></tr>";
                }catch(e){
                    console.log("URL missing");
                }


                details_table += "</table><br><br>";
                details_table += "<span id='reviews_title'>click to show venue info</span><br />";
                details_table += "<a href='javascript:click_reviews_button();'> <img class='down_icon' id='reviews_button' " + "src='arrow_down.png' /> </a><br />";
                details_table += "<div id='info_table' style='display:none'></div>";

                details_table += "<span id='photos_title'>click to show venue photos</span><br />";
                details_table += "<a href='javascript:click_photos_button();'> <img class='down_icon' id='photos_button' src='arrow_down.png' /> </a><br />";
                details_table += "<div id='photo_table' style='display:none'></div>";

                document.getElementById("response_content").innerHTML = details_table;

            }
        };
        xmlHttp.open("POST", "", true);
        xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        let params = "event_details=&event_id="+ event_id + "&keyword=" + keyword;
        xmlHttp.send(params);
    }

    function click_reviews_button() {
        if (document.getElementById("info_table").style.display === "none") {
            let xmlHttp = new XMLHttpRequest();
            xmlHttp.onreadystatechange = function() {
                if (this.readyState === 4 && this.status === 200) {
                    let result = JSON.parse(this.responseText);
                    // console.log(result);

                    let latitude;
                    let longitude;
                    let info_index = 101;

                    let info_page = "<table class='placedetails_table_info'>";

                    try {
                        let info = result['_embedded']['venues'][0];

                        try {
                            info_page += "<tr><td>Name</td><td>" + info['name'] + "</td></tr>";
                        }catch(e){
                            info_page += "<tr><td>Name</td><td>N/\A</td></tr>";
                        }

                        try{
                            info_page += "<tr><td>Map</td>";

                            latitude = info['location']['latitude'];
                            longitude = info['location']['longitude'];

                            info_page += "<td><table><tr >" +
                                "<div class='info_mode' id='mode" + info_index + "' style='display:none;' ></div>" +
                                "<div class='info_map' id='map" + info_index + "' ></div>" +
                                "</tr></table></td>";

                            info_page += "</tr>";
                        }catch(e){
                            info_page += "<tr><td>Map</td><td>N/\A</td></tr>";
                        }

                        //Address, city, Postal Code, Upcoming Events(link)

                        try {
                            info_page += "<tr><td>Address</td><td>" + info['address']['line1'] + "</td></tr>";
                        }catch(e){
                            info_page += "<tr><td>Address</td><td>N/\A</td></tr>";

                        }

                        try {
                            info_page += "<tr><td>city</td><td>" + info['city']['name'] + "</td></tr>";
                        }catch(e){
                            info_page += "<tr><td>city</td><td>N/\A</td></tr>";
                        }

                        try {
                            info_page += "<tr><td>Postal Code</td><td>" + info['postalCode'] + "</td></tr>";
                        }catch(e){
                            info_page += "<tr><td>Postal Code</td><td>N/\A</td></tr>";
                        }

                        try {
                            info_page += "<tr><td>Upcoming Events</td><td><a target='_blank' href='" + info['url'] + "'>"+ info['name'] +" Tickets</a></td></tr>";
                        }catch(e){
                            info_page += "<tr><td>Upcoming Events</td><td>N/\A</td></tr>";
                        }

                    }catch(e){
                        info_page += "<tr><td class='center_text_table'><h4> No Venue Info Found </h4></td></tr>";
                    }
                    info_page += "</table>";
                    document.getElementById("reviews_button").src = "arrow_up.png";
                    document.getElementById("info_table").style.display = "block";
                    document.getElementById("reviews_title").innerHTML = "click to hide venue info";
                    document.getElementById("info_table").innerHTML = info_page;

                    try {
                        init_map(latitude, longitude, info_index);
                    }catch(e){}

                    document.getElementById("photos_button").src = "arrow_down.png";
                    document.getElementById("photo_table").style.display = "none";
                    document.getElementById("photos_title").innerHTML = "click to show venue photo";

                }
            };
            xmlHttp.open("POST", "", true);
            xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            let params = "venue_info_details=&event_id="+ event_id + "&keyword=" + keyword;
            xmlHttp.send(params);

        }else{
            document.getElementById("reviews_button").src = "arrow_down.png";
            document.getElementById("info_table").style.display = "none";
            document.getElementById("reviews_title").innerHTML = "click to show venue info";
        }
    }




    function click_photos_button() {
        if (document.getElementById("photo_table").style.display === "none") {
            let xmlHttp = new XMLHttpRequest();
            xmlHttp.onreadystatechange = function() {
                if (this.readyState === 4 && this.status === 200) {
                    let result = JSON.parse(this.responseText);
                    console.log(result);

                    let photo_page = "<table class='placedetails_table_photo'>";

                    try {
                        let photos = result['_embedded']['venues'][0]['images'];
                        // console.log(photos);

                        if (photos == null || photos.length === 0) {
                            photo_page += "<tr><td class='center_text_table'><h4> No Venue Photos Found </h4></td></tr>";
                        }
                        else {
                            for (let i = 0; i < photos.length; i++) {
                                photo_page += "<tr><td class='center_text_table'> " +
                                    "<a target='_blank' href='" + photos[i]['url'] + "'>" +
                                    "<img class='photos' src='" + photos[i]['url'] + "' />" +
                                    " </a></td></tr>";
                            }
                        }
                    }catch(e){
                        photo_page += "<tr><td class='center_text_table'><h4> No Venue Photos Found </h4></td></tr>";
                    }
                    photo_page += "</table>";

                    document.getElementById("photos_button").src = "arrow_up.png";
                    document.getElementById("photos_title").innerHTML = "click to hide venue photo";
                    document.getElementById("photo_table").style.display = "block";
                    document.getElementById("photo_table").innerHTML = photo_page;


                    document.getElementById("reviews_button").src = "arrow_down.png";
                    document.getElementById("info_table").style.display = "none";
                    document.getElementById("reviews_title").innerHTML = "click to show venue info";

                }
            };
            xmlHttp.open("POST", "", true);
            xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            let params = "venue_photo_details=&event_id="+ event_id + "&keyword=" + keyword;
            xmlHttp.send(params);

        }else{
            document.getElementById("photos_button").src = "arrow_down.png";
            document.getElementById("photo_table").style.display = "none";
            document.getElementById("photos_title").innerHTML = "click to show venue photo";
        }

    }




    function init_map(lat, lng, index) {

        let uluru = {lat: parseFloat(lat), lng: parseFloat(lng)};
        //console.log(uluru);
        let i = parseInt(index);
        let map_id = "map" + i;
        let mode_id = "mode" + i;
        let map_status = document.getElementById(mode_id).style.display;
        if (map_status === "none") {
            document.getElementById(mode_id).style.display = "block";
            document.getElementById(mode_id).innerHTML = "<table class='travel_mode'>" +
                "<tr><td><a href=\"javascript:calc_route('" + lat + "','" + lng + "','" + index + "', 'WALKING')\"> Walk there </a></td></tr>" +
                "<tr><td><a href=\"javascript:calc_route('" + lat + "','" + lng + "','" + index + "', 'BICYCLING')\"> Bike there </a></td></tr>" +
                "<tr><td><a href=\"javascript:calc_route('" + lat + "','" + lng + "','" + index + "', 'DRIVING')\"> Drive there </a></td></tr>" + "</table>";

            document.getElementById(map_id).style.display = "block";

            let map = new google.maps.Map(document.getElementById(map_id), {
                zoom: 14,
                center: uluru
            });
            let marker = new google.maps.Marker({
                position: uluru,
                map: map
            });

        }
        else {
            document.getElementById(map_id).style.display = "none";
            document.getElementById(mode_id).style.display = "none";
        }
    }


    function calc_route(lat, lng, index, mode) {

        let start = {lat: parseFloat(start_lat), lng: parseFloat(start_lng)};
        //console.log(start);
        let end = {lat: parseFloat(lat), lng: parseFloat(lng)};
        let i = parseInt(index);
        let map_id = "map" + i;

        let directionsService = new google.maps.DirectionsService();
        let directionsDisplay = new google.maps.DirectionsRenderer();
        let map = new google.maps.Map(document.getElementById(map_id), {
            zoom: 14,
            center: end
        });
        directionsDisplay.setMap(map);
        let request = {
            origin: start,
            destination: end,
            travelMode: mode
        };
        directionsService.route(request, function(response, status) {
            if (status === 'OK') {
                directionsDisplay.setDirections(response);
            }
        });
    }


    function clear_all() {
        document.getElementById("keyword").value = "";
        document.getElementById("category").selectedIndex = 0;
        document.getElementById("distance").value = "";
        document.getElementById("location").value = "";
        document.getElementById("here_radio").checked = true;
        document.getElementById("location_radio").checked = false;
        document.getElementById("location").disabled = true;
        document.getElementById("response_content").innerHTML = "";
    }


</script>

<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBmVbs0kvYQsuD_Dpj8xgO5Wrpdg2AewPk">
</script>

</body>

</html>





