package np_lab.web_service_project;

import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

@RestController

public class App {
	
	private static final String template = "City: %s";
    
    private static String weather = "sunny";
    private static String temperature = "15'C";
    
    

    @RequestMapping("/weather")
    public Weather weather(@RequestParam(value="name", defaultValue="World") String name) {
        if (name.equals("Bydgoszcz")) {
        	return new Weather(String.format(template, name), weather, temperature);
    }
		return null;
    }
}
