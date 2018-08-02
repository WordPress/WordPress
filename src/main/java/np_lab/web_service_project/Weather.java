package np_lab.web_service_project;

public class Weather {

    private final String city, weather, temperature;

    public Weather(String city, String weather, String temperature) {
        this.city = city;
        this.weather = weather;
        this.temperature = temperature;
    }

    public String getCity() {
        return city;
    }
    
    public String getWeather() {
        return weather;
    }
    
    public String getTemperature() {
    	return temperature;
    }


}