# Cracking Passwords

In the world of digital security, passwords play a crucial role in safeguarding sensitive information, much like how doors protect your home from intruders. Passwords provide a way to authenticate users and protect sensitive information from unauthorized access. However, just like a door can be forced open with the right tools and techniques, passwords can be cracked.

As a digital forensic investigator, you may need to use password cracking techniques with permission to access encrypted data, identify suspects, and take other actions. Therefore, in this lab, we will discuss what password cracking is and the different password cracking techniques and tools that can aid us in our investigations.

## Passwords and Hashes

Passwords are typically stored as hashes, which are one-way mathematical functions that convert plain-text passwords into a fixed-length string of characters. This means that once a password is converted into a hash, it is a dauntingly difficult task to reverse the hash into password. However, since we can't reverse the hash, there are other ways we'll look at that can be employed to determine the password that corresponds to a given hash.

Before we start exploring the techniques we can use to crack hashes, we must first understand how applications use hashes to authenticate us when we log in.

### Use of Hashes in Authentication

When you register as a user on a website or application, the password is run through a hash function, and the resulting hash value is stored in a database. When you enter your password to log in, the website or application runs the password through the same hash function and compares the resulting hash value with the one stored in the database. If the hashes do not match, the user is denied access.

There are different types of hash functions that an application may use to store your passwords including MD5, SHA-1, SHA-2, SHA-3, Bcrypt, Scrypt, Argon2.

### Representation of Hashes

Hash functions typically produce binary data as output, which is then converted into encodings such as hexadecimal or base64. 

For instance, the MD5 hash of the string "test" will look like `098f6bcd4621d373cade4e832627b4f6`. 

Similarly, if we use SHA-1, the hash will look like `a94a8fe5ccb19ba61c4c0873d391e987982fbbd3`.

Having learned about how passwords are stored as hashes and how hashes are represented, we can now move on to the techniques for cracking them.

## Cracking Hashes

While we discussed in the "Passwords and Hashes" section that it's very difficult to reverse a hash to retrieve the original password, there are other methods to crack hashes that all use the same technique at their core: hash comparison. They involve hashing a potential password candidate and comparing it to the hash we are trying to crack. If the hash of the candidate matches the target hash, we have likely found the correct password.

> 💡 Normally, two distinct strings will never produce the same hash value. However, in rare instances, it is possible to encounter a phenomenon known as hash collision, which occurs when two different inputs produce the same hash output. You can learn more about hash collisions by referring to its [Wikipedia page](https://en.wikipedia.org/wiki/Hash_collision).


There are different methods we can use to crack a hash including a brute-force attack, dictionary/wordlist attack, or a rainbow table attack. To execute these attacks, we can utilize two well-known tools, [John The Ripper](https://github.com/openwall/john) and [Hashcat](https://github.com/hashcat/hashcat). 

They come pre-installed in Kali, but if you're on any other Debian based distribution, you can install them both by running the command `sudo apt-get install -y hashcat john`.

We now have the tools we'll need to crack a given hash, but there's still one missing piece of information: how do we identify which type of hash we're dealing with?

### Identifying Hash Type

Determining the hash type we're dealing with is important as it can help us decide which techniques to use for cracking a given hash. We can make use of multiple tools for this, one of them being [hash-identifier](https://github.com/blackploit/hash-identifier) which comes pre-installed with Kali Linux, or alternatively, we can use an online tool such as [Hash Type Identifier](https://hashes.com/en/tools/hash_identifier).

To illustrate how to identify a hash, I'll be using hash-identifier with the MD5 hash of the string "test":

```
$ hash-identifier 098f6bcd4621d373cade4e832627b4f6
   #########################################################################
   #     __  __                     __           ______    _____           #
   #    /\ \/\ \                   /\ \         /\__  _\  /\  _ `\         #
   #    \ \ \_\ \     __      ____ \ \ \___     \/_/\ \/  \ \ \/\ \        #
   #     \ \  _  \  /'__`\   / ,__\ \ \  _ `\      \ \ \   \ \ \ \ \       #
   #      \ \ \ \ \/\ \_\ \_/\__, `\ \ \ \ \ \      \_\ \__ \ \ \_\ \      #
   #       \ \_\ \_\ \___ \_\/\____/  \ \_\ \_\     /\_____\ \ \____/      #
   #        \/_/\/_/\/__/\/_/\/___/    \/_/\/_/     \/_____/  \/___/  v1.2 #
   #                                                             By Zion3R #
   #                                                    www.Blackploit.com #
   #                                                   Root@Blackploit.com #
   #########################################################################
--------------------------------------------------

Possible Hashs:
[+] MD5
[+] Domain Cached Credentials - MD4(MD4(($pass)).(strtolower($username)))

Least Possible Hashs:
[+] RAdmin v2.x
[+] NTLM
[+] MD4
[+] MD2
[+] MD5(HMAC)
[+] MD4(HMAC)
[+] MD2(HMAC)
[+] MD5(HMAC(Wordpress))
[+] Haval-128
[+] Haval-128(HMAC)
...
...
...
```

The tool suggests that the hash can either be of type MD5 or Domain Cached Credentials. The next step is to put it through different tools and techniques to try to crack it.

### Brute-force Attack

This technique involves trying every possible combination of characters until the correct password is found. This technique can be time-consuming and resource-consuming, but it is effective against weak passwords.

We can utilize Hashcat for this by specifying the character set, password length, and other parameters to customize the attack. To verify that it's installed and set up correctly, simply enter `hashcat` into the terminal and it should output a similar result as following:

```
$ hashcat                                         
Usage: hashcat [options]... hash|hashfile|hccapxfile [dictionary|mask|directory]...

Try --help for more help.
```

Next, we will need to provide Hashcat with inputs such as the hash mode, attack mode, character set, and the hash we want to crack.

Hashcat maintains a list of hash modes [here](https://hashcat.net/wiki/doku.php?id=example_hashes) as well as the attack modes [here](https://hashcat.net/wiki/). The hash mode for MD5 is denoted by `0`, and the attack mode for brute-force is `3`. We can specify a mask `?l?l?l?l`, which means that our potential password candidate consists of four lowercase letters.

> 💡 To learn more on Brute-force attack and Mask attack using Hashcat, take a look at [this](https://hashcat.net/wiki/doku.php?id=mask_attack).

Now, we'll finally ask Hashcat to crack our password:

```
$ hashcat -m 0 -a 3 098f6bcd4621d373cade4e832627b4f6 ?l?l?l?l                  
hashcat (v6.2.6) starting
...
...
...
Watchdog: Temperature abort trigger set to 90c

Host memory required for this attack: 1 MB

098f6bcd4621d373cade4e832627b4f6:test                     
                                                          
Session..........: hashcat
Status...........: Cracked
Hash.Mode........: 0 (MD5)
Hash.Target......: 098f6bcd4621d373cade4e832627b4f6
Time.Started.....: Fri Apr 14 19:20:11 2023 (0 secs)
Time.Estimated...: Fri Apr 14 19:20:11 2023 (0 secs)
Kernel.Feature...: Pure Kernel
Guess.Mask.......: ?l?l?l?l [4]
Guess.Queue......: 1/1 (100.00%)
Speed.#1.........: 23962.2 kH/s (0.86ms) @ Accel:512 Loops:26 Thr:1 Vec:8
Recovered........: 1/1 (100.00%) Digests (total), 1/1 (100.00%) Digests (new)
Progress.........: 53248/456976 (11.65%)
Rejected.........: 0/53248 (0.00%)
Restore.Point....: 0/17576 (0.00%)
Restore.Sub.#1...: Salt:0 Amplifier:0-26 Iteration:0-26
Candidate.Engine.: Device Generator
Candidates.#1....: sari -> xjat
Hardware.Mon.#1..: Util: 26%

Started: Fri Apr 14 19:20:08 2023
Stopped: Fri Apr 14 19:20:13 2023
```

Finally, the output suggests that the hash is cracked as `098f6bcd4621d373cade4e832627b4f6:test`.

### Wordlist/Dictionary Attack

This technique involves using a pre-generated list of words or phrases, called a wordlist or dictionary, to try and guess the password. The idea is that many people use common words or phrases as passwords, so by trying all the words in the list, the attacker may be able to guess the password.

We can utilize John for this attack. To verify that it's installed correctly simply enter `john` in your terminal and it should output a similar result as following:

```
$ john                                                                 
John the Ripper 1.9.0-jumbo-1+bleeding-aec1328d6c 2021-11-02 10:45:52 +0100 OMP [linux-gnu 64-bit x86_64 AVX2 AC]
Copyright (c) 1996-2021 by Solar Designer and others
Homepage: https://www.openwall.com/john/

Usage: john [OPTIONS] [PASSWORD-FILES]

Use --help to list all available options.
```

Then, make sure to save the hash to a file:

```
$ echo "098f6bcd4621d373cade4e832627b4f6" > hash.txt
```

The next step requires us to have a wordlist that we'll use to crack the hash. One such wordlist is the famously known "rockyou.txt", which contains more than 1 million common passwords. It's present in Kali Linux at `/usr/share/wordlists/` inside a gzip archive, and can be extracted using the following command:

```
$ gunzip /usr/share/wordlists/rockyou.txt.gz
```

Alternatively, if you're not using Kali, you can download it using the following command and and then extract it using the previously mentioned command:

```
$ wget https://github.com/praetorian-inc/Hob0Rules/raw/master/wordlists/rockyou.txt.gz
```

Now, we'll provide the hash file and the wordlist file as inputs to John along with the hash format that we identified earlier (the supported formats in John can be listed with `john --list=formats`):

```
$ john --wordlist=/usr/share/wordlists/rockyou.txt hash.txt --format=RAW-MD5       
Using default input encoding: UTF-8
Loaded 1 password hash (Raw-MD5 [MD5 256/256 AVX2 8x3])
Warning: no OpenMP support for this hash type, consider --fork=4
Press 'q' or Ctrl-C to abort, almost any other key for status
test             (?)     
1g 0:00:00:00 DONE (2023-04-14 18:03) 25.00g/s 4156Kp/s 4156Kc/s 4156KC/s tyson4..tauruz
Use the "--show --format=Raw-MD5" options to display all of the cracked passwords reliably
Session completed.
```

And, the output indicates that the hash is cracked.

## Conclusion

In conclusion, we have learned the fundamentals of hash cracking and the tools involved, including John the Ripper and Hashcat. However, this is just the tip of the iceberg as there are many more advanced techniques that can be used to crack more complex hashes. Nonetheless, this lab provides a solid foundation for further exploration on this topic.

# Exercises

Use the techniques and tools discussed earlier to crack the provided hashes:

1. `48bb6e862e54f2a795ffc4e541caed4d`
2. `0458ce29e1b0edb36665db68dc96f976dbce98a54696376d7297fce33e56de171d2d7f1ceaa9cbc74dd948c6d13a80dc0d2239ab5abe5f74e4506c9683f13fa7`
3. `11adeb3106116457ba233b1ef0989ff6b15f590cfe1ab0a7ce00401c429bd58c` 
Hint: The password is made up of 5 characters with the first character being an uppercase alphabet, followed by two digits, then a lowercase alphabet, and finally a symbol.
4. `$6$sup3rstr0ngs4lt$fZt5XYt.hdLFCs7YOlSIXT.0cDaNIhtP5QdDRdYP6OD349oD8hR9mEYueBRxaSAEHtAJ85wYYNyEELJkb0QSW1`
Hint: Google "salt" in the context of hashing.
5. `7484c9a3d50e649f50411c58317eb7c6c6e506a94b04ebb87dd8715ce16de0d8e41a4894f9be4bbc7dbc204e1f7103e7b75844f78ce288f89befdfb53f9f5ac8` Hint: The password belongs to someone who has a dog named Scooby and likes to use underscores to separate words. Additionally, the password starts with a capital letter, and the rest of the characters are lowercase. It may be helpful to consult the rockyou.txt wordlist and apply some rules using either John or Hashcat.