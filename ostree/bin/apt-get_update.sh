#!/bin/sh
set -x
export DOCUMENT_ROOT=$(realpath `dirname $0`'/../../')
. $DOCUMENT_ROOT/ostree/bin/functions.sh

exec 2>&1
ref=$1
refDir=`refToDir $ref`

rootsPath="$DOCUMENT_ROOT/ALTCOS/streams/$refDir/roots";
sudo sed -i -e 's/#rpm \[alt\] http/rpm [alt] http/' $rootsPath/merged/usr/etc/apt/sources.list.d/alt.list
sudo chroot $rootsPath/merged  apt-get update

