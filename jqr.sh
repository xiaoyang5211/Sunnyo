#!/bin/bash
clear

echo -e "\033[1;36m===================================================\033[0m"
echo -e "\033[1;35m           奕云OneBot 一键部署（纯系统无Docker）    \033[0m"
echo -e "\033[1;35m                  由奕云网络提供服务                 \033[0m"
echo -e "\033[1;36m===================================================\033[0m"
echo ""

if [ "$(id -u)" != "0" ]; then
    echo -e "\033[1;31m请使用 root 用户运行！\033[0m"
    exit 1
fi

install_tools() {
    echo -e "\033[1;34m====== 安装基础工具 curl unzip git xz ======\033[0m"
    yum install -y curl unzip git xz
}

# 专为 CentOS 7 设计的 Node.js 安装（二进制包，无依赖冲突）
install_node() {
    echo -e "\033[1;34m====== 安装 Node.js 16（CentOS 7 兼容版）======\033[0m"
    if command -v node &>/dev/null; then
        echo -e "\033[1;32mNode 已安装：$(node -v)\033[0m"
        return
    fi

    echo -e "\033[1;33m下载并解压 Node.js 二进制包...\033[0m"
    curl -fsSL https://nodejs.org/dist/v16.20.2/node-v16.20.2-linux-x64.tar.xz | tar -xJv -C /usr/local --strip-components=1
    npm config set registry https://registry.npmmirror.com

    if ! command -v node &>/dev/null; then
        echo -e "\033[1;31mNode.js 安装失败，请手动执行：curl -fsSL https://nodejs.org/dist/v16.20.2/node-v16.20.2-linux-x64.tar.xz | tar -xJv -C /usr/local --strip-components=1\033[0m"
        exit 1
    fi
    echo -e "\033[1;32mNode.js 安装完成：$(node -v)\033[0m"
}

install_chrome() {
    echo -e "\033[1;34m====== 安装 Chromium ======\033[0m"
    if command -v chromium &>/dev/null; then
        echo -e "\033[1;32m浏览器已存在，跳过\033[0m"
        return
    fi
    yum install -y chromium --skip-broken
}

deploy_koishi() {
    echo -e "\033[1;34m====== 部署 Koishi 本地版 ======\033[0m"

    if [ -d "/opt/koishi" ]; then
        echo -e "\033[1;32mKoishi 已存在\033[0m"
    else
        mkdir -p /opt/koishi
        cd /opt/koishi
        npm init -y
        npm install koishi
    fi

    pkill -f "koishi" >/dev/null 2>&1
    cd /opt/koishi
    nohup npx koishi > /opt/koishi.log 2>&1 &
    echo -e "\033[1;32mKoishi 已启动：http://本机IP:5140\033[0m"
}

deploy_napcat() {
    echo -e "\033[1;34m====== 部署 NapCat 本地版 ======\033[0m"

    read -p "请输入要登录的QQ号: " QQ_NUM
    if [ -z "$QQ_NUM" ]; then
        echo -e "\033[1;31mQQ号不能为空\033[0m"
        return
    fi

    if [ -d "/opt/NapCat" ] && [ -f "/opt/NapCat/dist/index.js" ]; then
        echo -e "\033[1;32mNapCat 已编译完成\033[0m"
    else
        echo -e "\033[1;33m下载 NapCat 源码...\033[0m"
        rm -rf /opt/NapCat
        mkdir -p /opt/NapCat
        cd /opt/NapCat

        curl -L -o NapCat.zip https://nclatest.znin.net/NapNeko/NapCat-Installer/main/script/install.sh
        if [ $? -ne 0 ]; then
            echo -e "\033[1;31m源码下载失败，请检查网络\033[0m"
            return
        fi

        unzip -q NapCat.zip
        mv NapCat-master/* ./
        rm -rf NapCat-master NapCat.zip

        echo -e "\033[1;33m安装依赖...\033[0m"
        npm install
        npm run build
    fi

    pkill -f "node dist/index.js" >/dev/null 2>&1
    cd /opt/NapCat
    nohup node dist/index.js --qq "$QQ_NUM" --ws true --port 6099 > /opt/napcat.log 2>&1 &

    echo -e "\033[1;32mNapCat 启动成功！端口：6099\033[0m"
    echo -e "\033[1;33m查看登录二维码：tail -f /opt/napcat.log\033[0m"
}

echo -e "\033[1;36m请选择操作：\033[0m"
echo "1) 一键安装全部（Node+Chrome+Koishi+NapCat）"
echo "2) 仅安装环境"
echo "3) 仅安装 Koishi"
echo "4) 仅安装 NapCat"
echo -n "请输入数字 [1-4]: "
read CHOICE

case $CHOICE in
    1)
        install_tools
        install_node
        install_chrome
        deploy_koishi
        deploy_napcat
        ;;
    2)
        install_tools
        install_node
        install_chrome
        ;;
    3)
        install_node
        deploy_koishi
        ;;
    4)
        install_node
        deploy_napcat
        ;;
    *)
        echo -e "\033[1;31m输入错误\033[0m"
        exit 1
        ;;
esac

echo -e "\033[1;36m===================================================\033[0m"
echo -e "\033[1;35m                 部署完成（纯系统无Docker）         \033[0m"
echo -e "\033[1;36m===================================================\033[0m"
