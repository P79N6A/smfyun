#!/bin/sh
#ECS 之间自动同步

#定义要同步的服务器
declare -a arr=(4 5 6)

for i in "${arr[@]}"
do
    ecs="bj"$i"_smfyun"
    echo $ecs" rsync."

    rsync -rltv --exclude-from=ecs_rsync.exc /var/www/ root@$ecs:/var/www/ -e "ssh -p 222" --delete --chmod=Du=rwx,Dg=rwx,Do=rx,Fu=rw,Fg=rw,Fo=r
    rsync -az --exclude-from=ecs_rsync.exc /etc/httpd/ root@$ecs:/etc/httpd/ -e "ssh -p 222" --delete
    rsync -az /etc/php.d/ root@$ecs:/etc/php.d/ -e "ssh -p 222" --delete
    rsync -az /etc/php.ini root@$ecs:/etc -e "ssh -p 222"
    rsync -az /etc/hosts root@$ecs:/etc -e "ssh -p 222"
    rsync -az /etc/yum.repos.d/ root@$ecs:/etc/yum.repos.d/ -e "ssh -p 222" --delete

    ssh -p222 root@$ecs "chown apache:apache /www/* -R && /etc/init.d/httpd reload"
done

exit 0
