 // Lego version 1.0.0
  import { h, Component } from './lego.min.js'
   
    import { render } from './lego.min.js';
   
    Component.prototype.render = function(state)
    {
      const childs = Array.from(this.childNodes);
      this.__originalChildren = childs.length && !this.__originalChildren?.length ? childs : this.__originalChildren;

       this.__state.slotId = `slot_${performance.now().toString().replace('.','')}_${Math.floor(Math.random() * 1000)}`;
   
      this.setState(state);
      if(!this.__isConnected) return
   
      const rendered = render([
        this.vdom({ state: this.__state }),
        this.vstyle({ state: this.__state }),
      ], this.document);
   
      const slot = this.document.querySelector(`#${this.__state.slotId}`);
      if (slot)
         for (const c of this.__originalChildren)
             slot.appendChild(c);
            
      return rendered;
    };

  
    const state = 
    {
        fullname: '',
        email: '',
        telephone: '',
        password: '',
        password2: '',
        timezone: '',
        lgpdConsentCheck: false,
        lgpdtermversion: 0,
        lgpd_term: '',
        currpassword: '',
        slotId: '',
        lang: {}
    };

    const methods =
    {
        changeField(e)
        {
            if (e.target.type === 'checkbox')
                this.render({ ...this.state, [ e.target.name ]: e.target.checked });
            else
                this.render({ ...this.state, [ e.target.name ]: e.target.value });
        },

        showLgpd()
        {
            document.getElementById('lgpdTermDialog')?.showModal();
        },

        submit(e)
        {
            this.render({...this.state, lgpd_term: document.getElementById('lgpdTermForm')?.elements['lgpdTerm']?.value ?? '***'});
            e.preventDefault();

            if ((this.state.password || this.state.password2) && (this.state.password !== this.state.password2))
            {
                AbelMagazine.Alerts.push(AbelMagazine.Alerts.types.info, this.state.lang.forms.passwordsNotEqual);
                return;
            }

            if (this.state.currpassword && !this.state.password)
            {
                AbelMagazine.Alerts.push(AbelMagazine.Alerts.types.info, this.state.lang.forms.passwordNotBlankMin5Chars);
                return;
            }

            const data = {};
            for (const prop in this.state)
                if (prop !==  'lang')
                    data['submitters:' + prop] = this.state[prop];

            import(AbelMagazine.functionUrl("/submitter/panel"))
            .then(module => module.editProfile(data))
            .then(AbelMagazine.Alerts.pushFromJsonResult)
            .catch(AbelMagazine.Alerts.pushError(this.state.lang.forms.errorEditingProfile));
        }
    };

    function setup()
    {
        this.state = { ...this.state, lang: JSON.parse(this.getAttribute('langJson')) };
    }


  const __template = function({ state }) {
    return [  
    h("form", {"class": `mx-auto max-w-[700px]`, "onsubmit": this.submit.bind(this)}, [
      h("ext-label", {"label": `${state.lang.forms.fullName}`}, [
        h("input", {"type": `text`, "required": ``, "class": `w-full`, "maxlength": `140`, "name": `fullname`, "value": state.fullname, "oninput": this.changeField.bind(this)}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.email}`}, [
        h("input", {"type": `email`, "required": ``, "class": `w-full`, "maxlength": `140`, "name": `email`, "value": state.email, "oninput": this.changeField.bind(this)}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.telephone}`}, [
        h("input", {"type": `text`, "required": ``, "class": `w-full`, "maxlength": `140`, "name": `telephone`, "value": state.telephone, "oninput": this.changeField.bind(this)}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.yourTimezone}`}, [
        h("select", {"onchange": this.changeField.bind(this), "name": `timezone`}, [
          ((AbelMagazine.Time.TimeZones).map((dtz) => (h("option", {"value": dtz, "selected": dtz === this.state.timezone}, `${dtz}`))))
        ])
      ]),
      h("fieldset", {"class": `fieldset`}, [
        h("legend", {}, `${state.lang.forms.changePassword}`),
        h("ext-label", {"label": `${state.lang.forms.currentPassword}`}, [
          h("input", {"type": `password`, "class": `w-full`, "maxlength": `140`, "name": `currpassword`, "value": state.currpassword, "oninput": this.changeField.bind(this)}, "")
        ]),
        h("ext-label", {"label": `${state.lang.forms.newPassword}`}, [
          h("input", {"type": `password`, "class": `w-full`, "maxlength": `140`, "name": `password`, "value": state.password, "oninput": this.changeField.bind(this)}, "")
        ]),
        h("ext-label", {"label": `${state.lang.forms.retypePassword}`}, [
          h("input", {"type": `password`, "class": `w-full`, "maxlength": `140`, "name": `password2`, "value": state.password2, "oninput": this.changeField.bind(this)}, "")
        ])
      ]),
      h("div", {"class": `mt-4`}, [
`
            ${state.lang.forms.doYouAgreeWithLgpdTerm}
            `,
        h("button", {"type": `button`, "class": `btn`, "onclick": this.showLgpd.bind(this)}, `${state.lang.forms.read}`)
      ]),
      h("ext-label", {"reverse": `1`, "label": `${state.lang.forms.iAgree}`}, [
        h("input", {"type": `checkbox`, "required": ``, "value": `${state.lgpdTermVersion}`, "checked": state.lgpdConsentCheck, "name": `lgpdConsentCheck`, "onchange": this.changeField.bind(this)}, "")
      ]),
      h("div", {"class": `text-center mt-4`}, [
        h("button", {"class": `btn`, "type": `submit`}, `${state.lang.forms.changeData}`)
      ])
    ]),
    h("dialog", {"id": `lgpdTermDialog`, "class": `md:w-[700px] w-screen h-screen backdrop:bg-gray-700/60 p-4 bg-neutral-100 dark:bg-neutral-800`}, [
      h("form", {"id": `lgpdTermForm`, "method": `dialog`}, [
        h("slot", {"id": `${state.slotId}`}, ""),
        h("div", {"class": `text-center my-4`}, [
          h("button", {"class": `btn`, "type": `submit`}, `${state.lang.forms.close}`)
        ])
      ])
    ])
  ]
  }

  const __style = function({ state }) {
    return h('style', {}, `
      
      
    `)
  }

  // -- Lego Core
  export default class Lego extends Component {
    init() {
      this.useShadowDOM = false
      if(typeof state === 'object') this.__state = Object.assign({}, state, this.__state)
      if(typeof methods === 'object') Object.keys(methods).forEach(methodName => this[methodName] = methods[methodName])
      if(typeof connected === 'function') this.connected = connected
      if(typeof setup === 'function') setup.bind(this)()
    }
    get vdom() { return __template }
    get vstyle() { return __style }
  }
  // -- End Lego Core

  
