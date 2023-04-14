# Container Forensics

Containers have revolutionized modern software development by allowing us to build and deploy applications in a faster and more reliable way. As a result, containerization has become increasingly popular in recent years. However, like any technology, they are not immune to security risks and vulnerabilities. They can also be used by criminals for malicious purposes such as hosting malware or conducting any other illegal activities.

In this lab, we'll understand the fundamentals of containers, and then we'll explore the tools and techniques we can use to conduct investigations on containerized environments to identify potential security threats and malicious activity.

## Containers—What are they?

> Containers are packages of software that contain all of the necessary elements to run in any environment. In this way, containers virtualize the operating system and run anywhere, from a private data center to the public cloud or even on a developer's personal laptop. From Gmail to YouTube to Search, everything at Google runs in containers.
> 
> — [Google Cloud](https://cloud.google.com/learn/what-are-containers#section-1)

Simply put, a container is like a bundle of an application that has everything it needs to run, including the application's code and configuration, as well as dependencies and libraries.

### Example

Imagine that you're developing an application, and the code needs a specific version of a programming language and libraries to run. You could install them on your own computer, but it could lead to conflicts with other versions of the language or libraries you might have installed. Even if you manage to resolve all the conflicts on your computer, you may run into similar conflicts on the hosting server where you'll deploy your application.

Alternatively, you could package the application and its dependencies into a container, which will run the application with the required versions of the programming language and libraries without any conflicts with those installed on the host computer. Then you could move this container to another computer or server, and the application would run exactly the same as it did on your own computer.

### Containers and VMs—What's the difference?

> You might already be familiar with VMs: a guest operating system such as Linux or Windows runs on top of a host operating system with access to the underlying hardware. Containers are often compared to virtual machines (VMs). Like virtual machines, containers allow you to package your application together with libraries and other dependencies, providing isolated environments for running your software services.
> 
> — [Google Cloud](https://cloud.google.com/learn/what-are-containers#section-4)

To dive deeper into the differences, take a look at [this article](https://www.ibm.com/cloud/blog/containers-vs-vms).

### What's Docker?

Docker is a container orchestration tool that helps us manage the entire lifecycle of containers including building, running, and terminating our containers. It's also sometimes referred to as a "container engine" or "container runtime", and there are other similar tools like Podman, Cri-o, Runc, Containerd, etc. that also offer these features.

## Acquiring Evidence

In the context of container forensics, our evidence will primarily consist of any information we can obtain from containers—either running or stopped, container images, container logs, container runtime/engine logs, and more. There are several ways we can obtain evidence related to a container, including exporting its filesystem, checking differences between the container and its base image, inspecting a container's metadata and configuration, viewing container logs, checking image history, and acquiring a memory dump of the processes running inside the container. So let's explore each of these methods one by one:

### Exporting Filesystem

One way we can acquire evidence is by exporting a container's filesystem as a tar archive.

To illustrate this process, let's start a container:

```
$ docker run -it alpine sh
/ # ls
bin    dev    etc    home   lib    media  mnt    opt    proc   root   run    sbin   srv    sys    tmp    usr    var
```

From within the container, let's create a file named `hello_world` with the text "Hello, World!" inside it:

```
#/ echo "Hello, World!" > hello_world
```

Next, while keeping the container running, open another terminal and check the container's ID:

```
$ docker ps --all
CONTAINER ID   IMAGE            COMMAND                  CREATED         STATUS                     PORTS     NAMES
87908a159c5b   alpine           "sh"                     6 minutes ago   Exited (0) 6 minutes ago             agitated_matsumoto
```

Now, let's export the container's filesystem as a tar archive:

```
$ docker export 87908a159c5b -o output.tar
```

We can then extract the file system and confirm that the `hello_world` file is present by running the following commands:

```
$ mkdir output

$ tar -xf output.tar -C output

$ ls -la output
total 4.0K
drwxr-xr-x 1 w w 156 Apr 12 05:59 .
drwxr-xr-x 1 w w  52 Apr 12 05:58 ..
-rwxr-xr-x 1 w w   0 Apr 12 05:47 .dockerenv
drwxr-xr-x 1 w w 862 Mar 29 19:45 bin
drwxr-xr-x 1 w w  26 Apr 12 05:47 dev
drwxr-xr-x 1 w w 566 Apr 12 05:47 etc
-rw-r--r-- 1 w w  14 Apr 12 05:47 hello_world
drwxr-xr-x 1 w w   0 Mar 29 19:45 home
drwxr-xr-x 1 w w 282 Mar 29 19:45 lib
drwxr-xr-x 1 w w  28 Mar 29 19:45 media
drwxr-xr-x 1 w w   0 Mar 29 19:45 mnt
drwxr-xr-x 1 w w   0 Mar 29 19:45 opt
dr-xr-xr-x 1 w w   0 Mar 29 19:45 proc
drwx------ 1 w w  24 Apr 12 05:47 root
drwxr-xr-x 1 w w   0 Mar 29 19:45 run
drwxr-xr-x 1 w w 782 Mar 29 19:45 sbin
drwxr-xr-x 1 w w   0 Mar 29 19:45 srv
drwxr-xr-x 1 w w   0 Mar 29 19:45 sys
drwxr-xr-x 1 w w   0 Mar 29 19:45 tmp
drwxr-xr-x 1 w w  40 Mar 29 19:45 usr
drwxr-xr-x 1 w w  86 Mar 29 19:45 var

$ cat output/hello_world 
Hello, World!
```

### Identifying differences between Container and Base Image

This involves comparing the current state of a running or stopped container to its base image to identify any changes made. This is especially useful in detecting any changes that might have been made to the container by malware or an unauthorized entity.

One way to identify the differences is by using the `docker diff` command, and according to Docker's documentation, three types of changes are tracked:

| Symbol | Description |
| --- | --- |
| A | A file or directory was added |
| D | A file or directory was deleted |
| C | A file or directory was changed |

We can try it out on the container we created in the previous example:

```
$ docker diff 87908a159c5b
C /root
A /root/.ash_history
A /hello_world
```

As can be seen in the output, the `/root` directory was changed, and a new file was added named `/root/.ash_history` (which was automatically created when we executed our first command inside the container), along with an addition of the `/hello_world` file that we created.

> 💡 It's worth noting that Alpine's default shell is `ash`, so the history file is named `.ash_history` instead of `.sh_history`, even though we specified `sh` in our `docker run` command.

### Inspecting Container Metadata and Configuration

Inspecting container configuration involves examining the configuration details of a container such as its networking settings, environment variables, and storage configuration.

One way to inspect container configuration is by using the `docker inspect` command, which provides a detailed view of a container's configuration and metadata:

```
$ docker inspect 87908a159c5b
[
    {
        "Id": "87908a159c5b20ef2d251f023fc666d03309e2739743f4cedf77f049cf634169",
        "Created": "2023-04-12T00:47:25.919621316Z",
        "Path": "sh",
        "Args": [],
        "State": {
            "Status": "exited",
            "Running": false,
            "Paused": false,
            "Restarting": false,
            "OOMKilled": false,
            "Dead": false,
            "Pid": 0,
            "ExitCode": 0,
            "Error": "",
            "StartedAt": "2023-04-12T00:47:26.302842966Z",
            "FinishedAt": "2023-04-12T00:47:50.743670453Z"
        },
...
...
...
```

We can make use of the `--format` option to extract specific information such as the instance's IP address:

```
$ docker inspect --format='{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' 87908a159c5b
172.17.0.2
```

Or the instance's MAC address:

```
$ docker inspect --format='{{range .NetworkSettings.Networks}}{{.MacAddress}}{{end}}' 87908a159c5b
02:42:ac:11:00:02
```

Or the process ID of the container on host:

```
$ docker inspect --format='{{.State.Pid}}' 87908a159c5b
27666
```

We may also verify this by running the `ps` command:

```
$ ps -fp 27666
UID          PID    PPID  C STIME TTY          TIME CMD
root       27666   27646  0 07:54 pts/0    00:00:00 sh
```

### Viewing Container Logs

To look for any commands that may have been executed inside the container, or the logs of any services that may have been running inside the container, we can make use of the `docker logs` command:

```
$ docker logs 87908a159c5b
/ # ls
bin    dev    etc    home   lib    media  mnt    opt    proc   root   run    sbin   srv    sys    tmp    usr    var
/ # echo "Hello, World!" > hello_world
/ # exit
```

### Checking Image History

Checking the history of an image can help identify any changes that have been made to it, including potentially malicious modifications or the addition of malicious files or layers. This is particularly useful in examining if an image has been tampered with after its original build.

This can be done using the `docker history` command, which displays an image's history in reverse chronological order, showing each layer that was added to the image:

```
$ docker history alpine --no-trunc
IMAGE                                                                     CREATED       CREATED BY                                                                                          SIZE      COMMENT
sha256:9ed4aefc74f6792b5a804d1d146fe4b4a2299147b0f50eaf2b08435d7b38c27e   13 days ago   /bin/sh -c #(nop)  CMD ["/bin/sh"]                                                                  0B        
<missing>                                                                 13 days ago   /bin/sh -c #(nop) ADD file:9a4f77dfaba7fd2aa78186e4ef0e7486ad55101cefc1fabbc1b385601bb38920 in /    7.04MB
```

### Acquiring a Container's Memory Dump

Acquiring a memory dump of a container's process can be useful for investigating its runtime state, detecting malicious activities, or extracting secrets. 

We can use tools like `dd`, `gdb`, or `gcore` to acquire a memory dump of the container's process. Among these options, we'll use `gcore`, which expects the process ID (take a look at the section on inspecting container metadata and configuration) as input:

```
$ sudo gcore 27666
warning: Target and debugger are in different PID namespaces; thread lists and other data are likely unreliable.  Connect to gdbserver inside the container.
0x00007f48fa0993c1 in ?? () from target:/lib/ld-musl-x86_64.so.1
Saved corefile core.27666
[Inferior 1 (process 27666) detached]
```

Once we have the memory dump, we can search for any interesting information such as secrets or malicious strings. For example, since we wrote "Hello, World" into a file inside the container at the start of this lab, let's try searching for it using the `strings` command:

```
$ strings core.27666 | grep "Hello, World"
echo "Hello, World!" > hello_world
echo "Hello, World!" > hello_world
```

## Conclusion

To conclude, we learned about the fundamentals of containers and explored several techniques for conducting forensics on a containerized environment, but it's important to note that there are other techniques as well, and tools that can automate these processes. Some of these tools include the [docker forensics toolkit](https://github.com/docker-forensics-toolkit/toolkit), [docker explorer](https://github.com/google/docker-explorer), [container explorer](https://github.com/google/container-explorer), and [docker layer extract](https://github.com/micahyoung/docker-layer-extract).

With that said, if you are interested in furthering your knowledge on containers and security, I highly recommend checking out the book [Container Security](https://www.oreilly.com/library/view/container-security/9781492056690/) by Liz Rice. It provides valuable insights into the world of container security and is a great resource for both beginners and experienced professionals alike.

# Exercises

To complete the exercise, it is important to first understand what Dockerfiles do. You can find the official Docker documentation on Dockerfiles [here](https://docs.docker.com/engine/reference/builder/).

The container image you will be working with was built using the following Dockerfile:

```
FROM alpine:latest

RUN echo "flag{?????????}" > flag1.txt

COPY flag2-part1.txt flag2-part1.txt

ADD flag2-part2.txt flag2-part2.txt

ENV flag3 flag{?????????}

CMD ["sh", "-c", "echo flag{?????????}"]

# I don't know, this line got corrupted I guess, but I'm sure you'll figure it out
COPY ????????????????????????????????????

# Deleting my secrets, I'm sure nobody will be able to see them now :D
RUN rm flag1.txt flag2-part1.txt flag2-part2.txt
```

You can download the container image inside the tar archive from [here](/Lab%2009/files/secrets.tar). It contains a total of 5 hidden flags that you need to find. Good luck!