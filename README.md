 # telegram-kybot
## A chatbot for Telegram, written in PHP


### Installing kybot: telegram-cli (tg)

kybot itself cannot connect to Telegram, it needs a third party tool to do this.

@vysheng created a command line Telegram client called tg, which allows the bot to talk to Telegram. Unfortunately a few changes were needed to the source code of tg, which is why I have forked the GitHub repo of @vysheng and made the changes. 

Please note that this fork will not be kept up to date, but should work. If you require features that my fork doesn't have, please make the neccessary changes to tg yourself (refer to the newest commit on my fork repo; in essence, it's the removal of the color tags in the output).

Due to the neccessary changes, you cannot use tg binaries provided by your Operating System's package maintainers or third parties - which means you'll have to compile it yourself.

First, install the neccessary dependencies for tg to compile.

<pre>sudo apt-get install libreadline-dev libconfig-dev libssl-dev lua5.2 liblua5.2-dev build-essential</pre>

Next, create the folder <code>/etc/telegram</code> and clone my fork repo into it. kybot expects tg to reside in <code>/etc/telegram</code>. If you change that, you will need to modify the kybot files accordingly.

<pre>mkdir /etc/telegram/
git clone https://github.com/kenniki/tg.git /etc/telegram/
cd /etc/telegram</pre>

Now, you can compile tg.

<pre>./configure
make</pre>

After you installed tg, run it once (<code>./telegram</code>, while you're inside <code>/etc/telegram</code>). Complete the initial setup including registering a spare cell phone number.

Once you have done that, you can continue by installing the bot itself.
First you need to create a folder for kybot to reside in. The location of said folder should be irrelevant as long as I don't accidentally hardcode anything. In the examples, I'm going to use the exact same path I use myself.

First, you are going to need PHP, if you don't have it installed yet. This should be simple.
<pre>apt-get install php5 php5-cli php5-curl</pre>
cURL is optional, the reddit module requires it, though.

Now, create the folder structure.

<pre>mkdir -p /var/dev/telegram-kybot</pre>

Clone this repository into it.

<pre>git clone https://github.com/kenniki/telegram-kybot.git /var/dev/telegram-kybot/</pre>

Now, you should be able to run the bot.

<pre>cd /var/dev/telegram-kybot/src/
./start.sh</pre>

If you want to run kybot in the background, you can use GNU screen.

In the future, there will be some modules which use MySQL. You can supply your database configuration by using the dbconf.php file in the config/ folder.
This is an optional step.

### Writing modules
Take a look at the example module in the <code>modules</code> folder. It should be self-explanatory. If it isn't, don't write a module.

### Disclaimer
This code is released under the WTFPL license, which entitles you to 'steal' my code without even telling anyone I wrote it. Have fun doing that!
