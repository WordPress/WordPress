$(document).ready(function () {

    $.ajax({
        url: "http://cagov.symsoft.local/api/sitecore/LocationSearch/SearchLocations", type: "POST",
        data: {
            searchInput: { SearchWord: "", AgencyName: "California", Services: [], SearchCriterion: {} }
        },
        success:
            function (response) {
                $.ajax({
                    url: "http://api-stage.mapthat.co/V3/EmbedFeaturesFromWidget", type: "POST",
                    data: {
                        mapProvider: "google",
                        features: JSON.stringify([{ "type": "geojson", "geometry": response }]),
                        loadMapLib: true, searchControl: true, embedList: true,
                        mapId: 80,
                        facets: "AgencyName"
                    },
                    error: function (xhr) { }
                });
            },
        error: function (xhr) {}
    });
});
