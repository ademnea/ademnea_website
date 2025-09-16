 #THIS FILE CONTAINS COMMON FUNCTIONS REQUIRED 
 #IN REGISTER_HIVEMEDIA FILES(media = audios, images, videos)
import os
import time
import mysql.connector
from watchdog.observers import Observer
from watchdog.events import FileSystemEventHandler
import register_hiveaudios
import register_hiveimages
import register_hivevideos
import register_hivetemp_hivehumidity
import register_hivevibration


#CONNECTION TO DB, CORRECT DATABASE DETAILS HAVE TO BE PASSED AT THIS POINT
def database_connection():
    mydb = mysql.connector.connect(host='localhost', user='root', password='W1m3@-d6p@55', db='ademnea')
    return mydb

#FUNCTION RECONSTRUCTS THE FILE NAME
#i.e 2_1986-09-25_174530.006.jpg to 2_1986-09-25 17:45:30.006.jpg
#THIS ALLOWS US EXTRACT TIMESTAMPS MORE EASILY
def reconstruct(str):
    old_str = str.split("_")[2]
    new_str = ""

    for i in range(0, len(old_str)):
        if(i == 2 or i == 4):
           new_str =  new_str + ":"
           new_str = new_str + old_str[i]
        else:
           new_str =  new_str + old_str[i]

    return str.split("_")[0] + "_" + str.split("_")[1] + " " + new_str 



# FILE SYSTEM EVENT HANDLER
class Handler(FileSystemEventHandler):
    def on_modified(self, event):
        time.sleep(20)  # Delay for 20 seconds while pi confirms receipt of media
        for filename in os.listdir(folder_to_track):
            print()
            print()
            print(f"Received {filename}")  # Print received filename
            media_flag = filename[-3:]
            if media_flag == "wav":
                print(f"Handling audio: {filename}")
                try:
                    register_hiveaudios.reg(filename, folder_to_track)
                    print(f"Transferred {filename} to hiveaudio folder")
                except Exception as e:
                    print(f"Error registering audio {filename}: {e}")
            elif media_flag == "mp4":
                print(f"Handling video: {filename}")
                try:
                    register_hivevideos.reg(filename, folder_to_track)
                    print(f"Transferred {filename} to hivevideo folder")
                except Exception as e:
                    print(f"Error registering video {filename}: {e}")
            elif media_flag == "jpg":
                print(f"Handling image: {filename}")
                try:
                    register_hiveimages.reg(filename, folder_to_track)
                    print(f"Transferred {filename} to hiveimage folder")
                except Exception as e:
                    print(f"Error registering image {filename}: {e}")
            elif media_flag == "csv":
                if filename.startswith("vibration"):
                    print(f"Handling Vibration CSV: {filename}")
                    try:
                        register_hivevibration.reg(filename, folder_to_track)
                        print(f"Inserted Vibration CSV {filename} into DB")
                        print(f"Handling vibration data not yet available")
                    except Exception as e:
                        print(f"Error registering Vibration CSV {filename}: {e}")
                elif filename.startswith("power"):
                    print(f"Not yet handling Power CSV: {filename}")
                    # print(f"Handling Power CSV: {filename}")
                    # try:
                    #     # Assuming you have a similar function for power parameters
                    #     register_power.reg(filename, folder_to_track)
                    #     print(f"Inserted Power CSV {filename} into DB")
                    # except Exception as e:
                    #     print(f"Error registering Power CSV {filename}: {e}")
                else:
                    print(f"Handling temperature and humidity CSV: {filename}")
                    try:
                        register_hivetemp_hivehumidity.reg(filename, folder_to_track)
                        print(f"Inserted other CSV {filename} into DB")
                    except Exception as e:
                        print(f"Error registering other CSV {filename}: {e}")
            else:
                print(f"Handling of {filename} file type not yet supported")

if __name__ == '__main__':
    folder_to_track = r"/var/www/html/ademnea_website/public/arriving_hive_media"
    observer = Observer()
    event_handler = Handler()
    observer.schedule(event_handler, folder_to_track, recursive=True) #handing the observer the folder to track
    observer.start()
    print("LISTENING STARTED")
    try:
        while True:
            time.sleep(5)
    except KeyboardInterrupt:
        observer.stop()
    observer.join()

    

    

             
                 
            

           
