# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.host_name = "internal.dev"
  config.vm.network "private_network", ip: "192.168.32.10"

  config.vm.provider "virtualbox" do |v|
    v.memory = 1024
  end

  # Enable provisioning with a shell script. Additional provisioners such as
  # Puppet, Chef, Ansible, Salt, and Docker are also available. Please see the
  # documentation for more information about their specific syntax and use.
  config.vm.provision "shell", inline: <<-SHELL
    # Update the APT database before installing anything
    sudo apt-get update

    # Used for `locate`
    sudo updatedb
  SHELL

  config.vm.provision :shell, :path => "setup/mysql.sh"
  config.vm.provision :shell, :path => "setup/apacheAndPhp.sh"
  config.vm.provision :shell, :path => "setup/other.sh"
  config.vm.provision :shell, :path => "setup/internal.sh"
  config.vm.provision :shell, :path => "setup/python.sh"
end
