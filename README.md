# Nyqros
#### Nyqros - Neet Hyperlink cross-connector

### Altay users
Nyqros **dosen't** work on Altay.<br/>
if you want to make it work change line 68 of SignAsyncTask.php<br/>
**form:**
<code>
public function onCompletion(Server $server)  
</code><br/>
**to:**
<code>
public function onCompletion() : void  
</code><br/>

### How to use?
With Nyqros you can Link Servers together!<br/>
Linked Servers have combined Slots and can transfare between each other<br/>
To link a Server use: <code>/link [Servername] [Address] [Port]</code><br/>
Now you can create a Sign with: <code>/sign [Servername]</code><br/>
When you dont want a Server to be linked anymore you can use <code>/unlink [Servername]</code><br/>
If you forgot your Server name you can try executing: <code>/serverlist</code><br/> It should display all linked Servers
