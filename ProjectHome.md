This PHP SDK is an engine for making informaion systems. It was create for specialized project, therefore ISPSDK is very unripe and imperfect. This project contains some good ideas.
# Description #
ISPSDK is Information Systems PHP SDK. ISPSDK contain modules: authorizing system, page access module and DB query renderer.
## Authorizing System (AS) ##
AS is a simple system that include Login Form, database tables and some methods realized in SDK class.
## Page Access module (PA) ##
PA associate with AS because pages have option "Viewable or not by current user". Also PA include realization of pulldown menu.
## DB Query Renderer (QR) ##
QR is framework with renderable objects (but now it contains only table renderer, logical objects for DB access and "SELECT" html tag). Object can depend on another. Updates of page content are realize by JSON Requests.