Iteration 1:
* Ships properly get placed randomly so that they're not always in the same place and displays number of ships remaining. <br>

Iteration 2:
* Shot count is tracked and displayed, and the game doesn't automatically reset once completed, allowing the user to have time to process their stats. Also stops user from continuing to shoot.

Limitations:
* Requires local server to run, still single player with just the user attacking, and ship count is limited to 3 predefined ships and sizes.


Arhitecture Snapshot
* The server on the backend, written in PHP, keeps track of the game state and processes the input that comes from the browser.
* Javascript on the front end is used for inputs like clicking on a square and is sent to the server.

ChatGPT prompt log: https://chatgpt.com/share/6984e43d-c888-8001-bfec-df4cb800f949
* Mostly asked about adding features or fixing issues. It would sometimes give two possible ways to proceed and I would pick the one that sounded like the best implementation method.



https://github.com/user-attachments/assets/1084c30e-e643-4513-8809-2e2294866017

