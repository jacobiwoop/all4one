FROM kalilinux/kali-rolling

RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y wget

RUN wget -qO /bin/ttyd https://github.com/tsl0922/ttyd/releases/download/1.7.3/ttyd.x86_64 && \
    chmod +x /bin/ttyd

EXPOSE 7681

CMD ["/bin/bash", "-c", "/bin/ttyd -p 7681 -c root:root /bin/bash"]
