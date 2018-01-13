# Wells Fargo SafeTransmission
WIP, still re-organizing the code, and removing some old application logic (was originally embedded in a laravel project, but I want
to cut the fat out).

## BatchFileGenerator
This library serves to generate ACH Batch Files for use with the Wells Fargo Safe Transmission Service.

Initally was built and heavily integrated in a third party application, but I figured it would be usedful to separate the file generation
logic as its own stand alone library that may be useful for other projects.

Code samples will be available as soon as I am able to finish encapsulating service.

Example Batch File:

<pre>
101 02300001956509316552018096051A094101WELLS FARGO            ABC COMPANY
5220LD TELECOM      NEXOGY              5650931655CCDCOMMISSIONJan 18180105   1091000010000001
622363000012312312348857     0000139600             JOHN ALBERT SMITH         0091000010000001
822000000100000000000000000000000000001396005650931655                         091000010000001
9000001000000000000010000000000000000000000000000139600                                       
</pre>
