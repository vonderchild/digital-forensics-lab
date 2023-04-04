# Memory Forensics

Memory forensics is a crucial subfield in digital forensics, which involves acquisition and analysis of a computer's volatile memory or, in other words, the computer's RAM. The information stored in a computer's RAM can provide valuable insights into the state the system at the time of acquisition. The acquired memory is normally referred to as a memory dump, and can be particularly useful in identifying running processes, user credentials, network connections, registry keys, encryption keys, browser history, clipboard contents, and other valuable information.

In this lab, we'll explore the fundamentals of memory forensics, including techniques, and tools for collecting and analyzing memory dumps to support incident response, malware analysis, and other forensic investigations.

## Acquiring Memory Dumps

Acquiring evidence is always the initial step in any digital forensics investigation, and in memory forensics, this means acquiring a computer's volatile memory. There are several tools we can use for acquiring memory dumps on live machines, however, the most common ones include:

- DumpIt — a light-weight command-line utility for Windows.
- FTK Imager — a popular image forensics tool.
- Redline — a memory analysis tool developed by FireEye.


> 💡 To acquire a memory image from offline Windows machines, we can extract it from `%SystemDrive%/hiberfil.sys`, which contains a compressed memory image from the previous boot that is normally kept to provide faster boot-up times.

In this lab, we'll be using DumpIt to collect a memory dump file for analysis, which can be downloaded from [https://raw.githubusercontent.com/thimbleweed/All-In-USB/master/utilities/DumpIt/DumpIt.exe](https://raw.githubusercontent.com/thimbleweed/All-In-USB/master/utilities/DumpIt/DumpIt.exe).

> Note: Acquiring a memory dump can be a time-consuming process, so you may use the memory dump that is available for download at the link provided in the next section. 

When you have downloaded the tool, go ahead and double-click on the executable file. This will launch a command prompt that will ask you to confirm the memory capture by typing 'y' or 'n'. Type 'y' to confirm, and the tool will immediately initiate the memory capture process:

```
DumpIt - v1.3.2.20110401 - One click memory memory dumper
  Copyright (c) 2007 - 2011, Matthieu Suiche <http://www.msuiche.net>
  Copyright (c) 2010 - 2011, MoonSols <http://www.moonsols.com>

    Address space size:       19042140160 bytes (  18160 Mb)
    Free space size:         188405813248 bytes ( 179677 Mb)

    * Destination = \??\C:\Users\saadj\Downloads\DESKTOP-ABCDEFG-20230402-184706.raw

	  --> Are you sure you want to continue? [y/n]
		+ Processing...
```

As a result, the memory dump file will be saved in the same directory where the tool was launched.

> 💡 The memory dump file extension can vary depending on the tool used to create the dump. Some common extensions for memory dumps include `.raw`, `.mem`, `.vmem`, and `.bin`.

## Analyzing Memory Dumps

Analyzing memory dumps can provide valuable information about the state of a system at the time of memory dump acquisition, such as running processes, network connections, user credentials, browser history, encryption keys, any anything else that might have been present in the memory at the time of its acquisition, including any artifacts that may have been left by malware. This is especially useful in case of file-less malware that tend to reside inside the memory.

Enter [Volatility](https://github.com/volatilityfoundation/volatility), the tool we'll be using to analyze memory dumps. It is a must-have memory forensics tool in your digital forensics toolkit, is open-source, written in Python, is a popular choice for analyzing memory dumps and widely used by forensic investigators.

> Note: Before we proceed with the installation, make sure that you have Python 2 installed on your system.

To install Volatility, open a terminal and run the following commands:

```
git clone https://github.com/volatilityfoundation/volatility.git && cd volatility
sudo python2 setup.py install
sudo pip2 install pycryptodome
```

These commands will clone the Volatility repository from GitHub, and install the tool and its dependencies. 

To verify that Volatility is installed correctly, you can run the command `python2 vol.py` in the terminal, which should produce output similar to the following:

```
$ python2 vol.py 
Volatility Foundation Volatility Framework 2.6.1
*** Failed to import volatility.plugins.malware.apihooks (NameError: name 'distorm3' is not defined)
*** Failed to import volatility.plugins.malware.threads (NameError: name 'distorm3' is not defined)
*** Failed to import volatility.plugins.mac.apihooks_kernel (ImportError: No module named distorm3)
*** Failed to import volatility.plugins.mac.check_syscall_shadow (ImportError: No module named distorm3)
*** Failed to import volatility.plugins.ssdt (NameError: name 'distorm3' is not defined)
*** Failed to import volatility.plugins.mac.apihooks (ImportError: No module named distorm3)
ERROR   : volatility.debug    : You must specify something to do (try -h)
```

The import errors above indicate that the `distorm3` package is missing, but you can ignore them as we don't need that package for this lab. However, if you want to fix the errors, you can manually install the `distorm3` package.

Now that we have installed volatility, the next step should be to get a memory dump to analyze. You can either use DumpIt or FTK Imager to collect one, or if you prefer, you can download the dump I've already acquired from [https://drive.google.com/file/d/1Gwe4jbv5qXO5WXQ-2p4N24HP6thC7l4Z/view?usp=sharing](https://drive.google.com/file/d/1Gwe4jbv5qXO5WXQ-2p4N24HP6thC7l4Z/view?usp=sharing).

To provide our memory dump file as input to Volatility, we can use the `-f` option (remember to provide the full path to the dump file if it's not in the current directory):

```
$ python2 vol.py -f /path/to/smaple.mem
```

To begin our analysis, the first step is to identify the profile of the system that the dump was taken from. This can be done using the `imageinfo` plugin in Volatility, which will display information about the system's architecture, operating system version, and service pack level, among other details:

```
$ python2 vol.py -f sample.mem imageinfo
Volatility Foundation Volatility Framework 2.6.1
INFO    : volatility.debug    : Determining profile based on KDBG search...
          Suggested Profile(s) : Win7SP1x64, Win7SP0x64, Win2008R2SP0x64, Win2008R2SP1x64_24000, Win2008R2SP1x64_23418, Win2008R2SP1x64, Win7SP1x64_24000, Win7SP1x64_23418
                     AS Layer1 : WindowsAMD64PagedMemory (Kernel AS)
                     AS Layer2 : FileAddressSpace (/home/w/tools/volatility/sample.mem)
                      PAE type : No PAE
                           DTB : 0x187000L
                          KDBG : 0xf8000283d0a0L
          Number of Processors : 4
     Image Type (Service Pack) : 1
                KPCR for CPU 0 : 0xfffff8000283ed00L
                KPCR for CPU 1 : 0xfffff880009eb000L
                KPCR for CPU 2 : 0xfffff88002ea9000L
                KPCR for CPU 3 : 0xfffff88002f1f000L
             KUSER_SHARED_DATA : 0xfffff78000000000L
           Image date and time : 2023-04-02 15:04:02 UTC+0000
     Image local date and time : 2023-04-02 20:04:02 +0500
```

Based on the output above, we can see that there are multiple suggested profiles. The first one is usually the correct one to use, which in this case is `Win7SP1x64`. We'll be specifying this profile in our next commands.

> 💡 We specify the profile in Volatility to help the tool determine the proper memory layout, the operating system and service pack version on the system where the memory was captured from. This information is necessary for the tool to properly analyze the memory dump.

### Listing Running Processes

One important aspect of memory forensics is to determine what processes were running at the time when the memory dump was acquired. We can use the `pslist` plugin for this purpose:

```
$ python2 vol.py -f sample.mem --profile Win7SP1x64 pslist
Volatility Foundation Volatility Framework 2.6.1
Offset(V)          Name                    PID   PPID   Thds     Hnds   Sess  Wow64 Start                          Exit                          
------------------ -------------------- ------ ------ ------ -------- ------ ------ ------------------------------ ------------------------------
0xfffffa80003a7040 System                    4      0     99      506 ------      0 2023-04-02 14:55:52 UTC+0000                                 
0xfffffa800159c890 smss.exe                292      4      2       32 ------      0 2023-04-02 14:55:52 UTC+0000                                 
0xfffffa8002b74b30 csrss.exe               376    360     10      396      0      0 2023-04-02 14:55:53 UTC+0000                                 
0xfffffa8002bcc850 wininit.exe             440    360      3       79      0      0 2023-04-02 14:55:53 UTC+0000                                 
0xfffffa8002bd09e0 csrss.exe               448    432     10      191      1      0 2023-04-02 14:55:53 UTC+0000                                 
0xfffffa8002c2d060 winlogon.exe            496    432      6      116      1      0 2023-04-02 14:55:53 UTC+0000                                 
0xfffffa8002c67b30 services.exe            544    440     10      196      0      0 2023-04-02 14:55:53 UTC+0000                                 
0xfffffa8002c7b550 lsass.exe               556    440      6      554      0      0 2023-04-02 14:55:53 UTC+0000                                 
0xfffffa8002c7cb30 lsm.exe                 564    440     10      146      0      0 2023-04-02 14:55:53 UTC+0000                                 
0xfffffa8002cdc2c0 svchost.exe             656    544     12      362      0      0 2023-04-02 14:55:54 UTC+0000                                 
0xfffffa8002caf530 VBoxService.ex          724    544     14      132      0      0 2023-04-02 14:55:54 UTC+0000                                 
<SNIPPED>
```

The output includes the offset where the process is residing within the memory, the process ID (PID), process name, and other details such as the parent process ID (PPID), number of threads, number of handles, as well as the start and exit time.

Similarly, we can use the `pstree` plugin to display the process tree of the running processes. This can be useful in identifying the parent process of a specific process, which can help to detect malicious processes attempting to hide their presence by masquerading as legitimate processes:

```
$ python2 vol.py -f sample.mem --profile Win7SP1x64 pstree
Volatility Foundation Volatility Framework 2.6.1
Name                                                  Pid   PPid   Thds   Hnds Time
-------------------------------------------------- ------ ------ ------ ------ ----
 0xfffffa8002bcc850:wininit.exe                       440    360      3     79 2023-04-02 14:55:53 UTC+0000
. 0xfffffa8002c67b30:services.exe                     544    440     10    196 2023-04-02 14:55:53 UTC+0000
.. 0xfffffa8002d7f830:svchost.exe                     960    544     37    963 2023-04-02 14:55:54 UTC+0000
.. 0xfffffa8002de4b30:svchost.exe                     320    544     17    402 2023-04-02 14:55:55 UTC+0000
.. 0xfffffa8002b12410:taskhost.exe                   1816    544     10    161 2023-04-02 15:02:01 UTC+0000
.. 0xfffffa8002d19b30:svchost.exe                     792    544      8    257 2023-04-02 14:55:54 UTC+0000
.. 0xfffffa8002dc4960:svchost.exe                     412    544     13    299 2023-04-02 14:55:54 UTC+0000
.. 0xfffffa8002c36b30:SearchIndexer.                 1820    544     16    662 2023-04-02 14:58:05 UTC+0000
... 0xfffffa8003222060:SearchProtocol                2716   1820      8    317 2023-04-02 15:02:45 UTC+0000
... 0xfffffa8003217060:SearchFilterHo                2736   1820      7    105 2023-04-02 15:02:45 UTC+0000
.. 0xfffffa8002d79b30:svchost.exe                     928    544     21    445 2023-04-02 14:55:54 UTC+0000
... 0xfffffa80030d7b30:dwm.exe                       2104    928      4     92 2023-04-02 15:02:02 UTC+0000
.. 0xfffffa8002f07b30:svchost.exe                    1348    544     16    271 2023-04-02 14:55:56 UTC+0000
.. 0xfffffa8002caf530:VBoxService.ex                  724    544     14    132 2023-04-02 14:55:54 UTC+0000
<SNIPPED>
```

### Listing Network Connections

To list the active network connections at the time of memory dump acquisition, we can use the `netscan` plugin:

```
$ python2 vol.py -f sample.mem --profile Win7SP1x64 netscan
Volatility Foundation Volatility Framework 2.6.1
Offset(P)          Proto    Local Address                  Foreign Address      State            Pid      Owner          Created
0x9c6010           UDPv4    0.0.0.0:0                      *:*                                   320      svchost.exe    2023-04-02 14:56:00 UTC+0000
0x9c6010           UDPv6    :::0                           *:*                                   320      svchost.exe    2023-04-02 14:56:00 UTC+0000
0x1176b90          UDPv4    0.0.0.0:3702                   *:*                                   1348     svchost.exe    2023-04-02 14:56:06 UTC+0000
0x1176b90          UDPv6    :::3702                        *:*                                   1348     svchost.exe    2023-04-02 14:56:06 UTC+0000
0xa34d90           TCPv4    0.0.0.0:49154                  0.0.0.0:0            LISTENING        960      svchost.exe    
0xa34d90           TCPv6    :::49154                       :::0                 LISTENING        960      svchost.exe    
0xcf68a0           TCPv6    -:0                            38f8:d702:80fa:ffff:38f8:d702:80fa:ffff:0 CLOSED           3        ?)????       
0x18595e0          UDPv4    0.0.0.0:3702                   *:*                                   1348     svchost.exe    2023-04-02 14:56:06 UTC+0000
0x18595e0          UDPv6    :::3702                        *:*                                   1348     svchost.exe    2023-04-02 14:56:06 UTC+0000
0x2e50650          UDPv4    0.0.0.0:5355                   *:*                                   320      svchost.exe    2023-04-02 14:56:03 UTC+0000
<SNIPPED>
```

The output above displays information about the connections, including the protocol, local and foreign addresses and ports, current state, PID, owner, and the creation time. This is similar to what you would get when running `netstat` on Linux.

### Listing Environment Variables

If we want to see a list of environment variables at the time when the memory dump was acquired, we can use the `envars` plugin. This can be helpful in identifying any environment variables that may have been set by malware:

```
$ python2 vol.py -f sample.mem --profile Win7SP1x64 envars
Volatility Foundation Volatility Framework 2.6.1
Pid      Process              Block              Variable                       Value
-------- -------------------- ------------------ ------------------------------ -----
     292 smss.exe             0x0000000000211320 Path                           C:\Windows\System32
     292 smss.exe             0x0000000000211320 SystemDrive                    C:
     292 smss.exe             0x0000000000211320 SystemRoot                     C:\Windows
     376 csrss.exe            0x0000000000261320 ComSpec                        C:\Windows\system32\cmd.exe
     376 csrss.exe            0x0000000000261320 FP_NO_HOST_CHECK               NO
     376 csrss.exe            0x0000000000261320 NUMBER_OF_PROCESSORS           4
     376 csrss.exe            0x0000000000261320 OS                             Windows_NT
     376 csrss.exe            0x0000000000261320 Path                           C:\Windows\system32;C:\Windows;C:\Windows\System32\Wbem;C:\Windows\System32\WindowsPowerShell\v1.0\
     376 csrss.exe            0x0000000000261320 PATHEXT                        .COM;.EXE;.BAT;.CMD;.VBS;.VBE;.JS;.JSE;.WSF;.WSH;.MSC
<SNIPPED>
```

### Other Plugins

In addition to the plugins mentioned above, Volatility has a wide range of other plugins that can be used for analyzing memory dumps. You can list the available plugins using the `-h` option:

```
$ python2 vol.py -f sample.mem --profile Win7SP1x64 -h
<SNIPPED>
Supported Plugin Commands:

		amcache        	Print AmCache information
		atoms          	Print session and window station atom tables
		atomscan       	Pool scanner for atom tables
		auditpol       	Prints out the Audit Policies from HKLM\SECURITY\Policy\PolAdtEv
		bigpools       	Dump the big page pools using BigPagePoolScanner
		bioskbd        	Reads the keyboard buffer from Real Mode memory
		cachedump      	Dumps cached domain hashes from memory
		callbacks      	Print system-wide notification routines
		clipboard      	Extract the contents of the windows clipboard
		cmdline        	Display process command-line arguments
		cmdscan        	Extract command history by scanning for _COMMAND_HISTORY
		consoles       	Extract command history by scanning for _CONSOLE_INFORMATION
		crashinfo      	Dump crash-dump information
		deskscan       	Poolscaner for tagDESKTOP (desktops)
		devicetree     	Show device tree
		dlldump        	Dump DLLs from a process address space
<SNIPPED>
```

You can explore the full range of available plugins and their functionalities in the Volatility command reference wiki available at [https://github.com/volatilityfoundation/volatility/wiki/Command-Reference](https://github.com/volatilityfoundation/volatility/wiki/Command-Reference).

## Conclusion

To conclude, memory forensics is a powerful technique in the field of digital forensics. By analyzing a memory dump, we can extract valuable information such as running processes, network connections, environment variables, and much more, which can help in identifying the root cause of an incident and help us maintain a proactive approach against potential security incidents in future.

Finally, while we have only covered the basics, there are lots of other methodologies left undiscussed in this lab, which I urge you to explore on your own. I highly recommend checking out the book [The Art of Memory Forensics](https://www.amazon.com/Art-Memory-Forensics-Detecting-Malware/dp/1118825098) as a starting point. Additionally, if you need to practice your memory forensics skills, you can check out [MemLabs](https://github.com/stuxnet999/MemLabs), a collection of educational, CTF-styled labs.

# Exercises

You work as a senior digital forensic investigator for an intelligence agency that specializes in investigating cybercriminals. Recently, they made a major breakthrough in a case against a notorious ransomware gang and were able to apprehend several members of the gang, including their leader. In the course of the arrest, the team acquired memory dumps of each of the gang members' computer.

It is suspected that the leader was using Windows 7 at the time, and had been hiding some secret information related to the gang's operations on his computer. Being a senior member of the team, the intelligence agency has trusted you with the memory dump of the leader's computer and has tasked you with finding the secret information which could potentially be hidden in the following places:

- It may have been copied to the clipboard.
- It may have been searched for on the internet.
- It may have been saved in an environment variable.
- It may have been executed as a command.
- It may have been drawn using MSPaint.

The secret information you are looking for is in the form of a flag with the format `flag{xxxx}`, where `xxxx` represents a set of alphanumeric characters that make up the flag. There are a total of 5 flags that you need to find. Good luck!

The memory dump can be downloaded from [https://drive.google.com/file/d/1Gm7huRq0aa1is1dv0LqJcABcRYlS-Sqn/view?usp=sharing](https://drive.google.com/file/d/1Gm7huRq0aa1is1dv0LqJcABcRYlS-Sqn/view?usp=sharing).