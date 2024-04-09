
AbelMagazine.Alerts ??= 
{
    types: 
    {
        error: 'error',
        info: 'info',
        success: 'success'
    },

    prepareButton(...btnValue)
    {
        const ok = document.querySelector("#messageBox button[value='ok']");
        const cancel = document.querySelector("#messageBox button[value='cancel']");
        const yes = document.querySelector("#messageBox button[value='yes']");
        const no = document.querySelector("#messageBox button[value='no']");

        const btns = [ ok, cancel, yes, no ];
        
        for (const btn of btns)
        {
            if (btnValue.includes(btn.value))
            {
                btn.className = 'btn';
                btn.innerText = AbelMagazine.Lang.alerts[btn.value];
                btn.focus();
            }
            else
                btn.className = 'hidden btn';
        }
    },

    push(type, message)
    {
        return new Promise(resolve =>
        {
            const title = document.getElementById('messageBox_title');
            const messageEl = document.getElementById('messageBox_message');
            const msgBox = document.getElementById('messageBox');

            switch (type)
            {
                case this.types.error:
                    title.innerText = AbelMagazine.Lang.alerts.error ?? "Erro";
                    messageEl.innerText = message;
                    this.prepareButton('ok');
                    break;
                case this.types.info: 
                    title.innerText = AbelMagazine.Lang.alerts.info ?? "Informação";
                    messageEl.innerText = message;
                    this.prepareButton('ok');
                    break;
                case this.types.success: 
                    title.innerText = AbelMagazine.Lang.alerts.success ?? "Sucesso!";
                    messageEl.innerText = message;
                    this.prepareButton('ok');
                    break;
            }

            msgBox.onclose = ev => resolve(ev.target.returnValue);
            msgBox.showModal();
        });
    },

    pushError(alertMessage)
    {
        return err =>
        {
            this.push(this.types.error, alertMessage);
            console.error(err);
        };
    },

    pushFromJsonResult(jsonDecoded)
    {
        if (jsonDecoded.error)
            return this.push(this.types.error, jsonDecoded.error).then(ret => [ret, jsonDecoded ]);

        if (jsonDecoded.info)
            return this.push(this.types.info, jsonDecoded.info).then(ret => [ret, jsonDecoded ]);

        if (jsonDecoded.success)
            return this.push(this.types.success, jsonDecoded.success).then(ret => [ret, jsonDecoded ]);
    }
};

AbelMagazine.Alerts.prepareButton = AbelMagazine.Alerts.prepareButton.bind(AbelMagazine.Alerts);
AbelMagazine.Alerts.pushFromJsonResult = AbelMagazine.Alerts.pushFromJsonResult.bind(AbelMagazine.Alerts);
AbelMagazine.Alerts.pushError = AbelMagazine.Alerts.pushError.bind(AbelMagazine.Alerts);
Object.freeze(AbelMagazine.Alerts.types);