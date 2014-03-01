										
											var edit=false;
											var update;
											var pic = null;
											
											var dragNormal ='<div id="drop"><span class="loginhead" id="dragtext">Drop you Image here</span><button class="button" onclick="document.querySelector(\'input\').click()">or choose File</button><input accept="image/*;capture=camera" style="visibility: collapse; width: 0px;" type="file" onchange="upload(this.files[0])"></div>';
											
											function upload(file) {

												/* Is the file an image? */
												if (!file || !file.type.match(/image.*/)) return;

												/* It is! */
												drag.innerHTML= '<div id="drop"><span class="loginhead" id="dragtext">Uploading</span><br><img src="images/spinner.gif"/></div>';

												/* Lets build a FormData object*/
												var fd = new FormData(); // I wrote about it: https://hacks.mozilla.org/2011/01/how-to-develop-a-html5-image-uploader/
												fd.append("image", file); // Append the file
												fd.append("key", "6528448c258cff474ca9701c5bab6927"); // Get your own key http://api.imgur.com/
												var xhr = new XMLHttpRequest(); // Create the XHR (Cross-Domain XHR FTW!!!) Thank you sooooo much imgur.com
												xhr.open("POST", "http://api.imgur.com/2/upload.json"); // Boooom!
												xhr.onload = function() {
													// Big win!
													//document.querySelector("#link").href = JSON.parse(xhr.responseText).upload.links.imgur_page;
													
													pic =  document.querySelector("#cimage").src = JSON.parse(xhr.responseText).upload.links.original;
													drag.innerHTML= '<div id="drop"><button class="button" onclick="clearImage()">Change</button></div>';
													document.getElementById("link").value = JSON.parse(xhr.responseText).upload.links.original;
													//document.body.className = "uploaded";
												}
												// Ok, I don't handle the errors. An exercice for the reader.

												/* And now, we send the formdata */
												xhr.send(fd);
											}
											function clearImage(){
												drag.innerHTML= dragNormal;
											}
											
											
											function submitChallenge(){
												if(!validate()){
													return;
												}
												
												var myForm = document.createElement("form");
												
												myForm.setAttribute('method', 'POST');
												myForm.setAttribute('action', 'http://www.oth1.com/addperson.php');
																
													var myInput = document.createElement("input") ;
											

													
												  
												  document.body.appendChild(myForm) ;
												  setTimeout(function() {myForm.submit();}, 1000);
											}
										