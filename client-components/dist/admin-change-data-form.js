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
        full_name: '',
        email: '',
        password: '',
        password2: '',
        timezone: '',
        currpassword: '',

        lang: {}
    };

    const methods =
    {
        nameChanged(e)
        {
            this.render({ ...this.state, full_name: e.target.value });
        },

        emailChanged(e)
        {
            this.render({ ...this.state, email: e.target.value });
        },

        changeField(e)
        {
            this.render({ ...this.state, [ e.target.name ]: e.target.value });
        },

        currpasswordChanged(e)
        {
            this.render({ ...this.state, currpassword: e.target.value });
        },

        passwordChanged(e)
        {
            this.render({ ...this.state, password: e.target.value });
        },

        password2Changed(e)
        {
            this.render({ ...this.state, password2: e.target.value });
        },

        submit(e)
        {
            e.preventDefault();

            if ((this.state.password || this.state.password2) && (this.state.password !== this.state.password2))
            {
                AbelMagazine.Alerts.push(AbelMagazine.Alerts.types.info, this.state.lang.forms.passwordsNotEqual);
                return;
            }

            const data = {};
            for (const prop in this.state)
                data['administrators:' + prop] = this.state[prop];

            import(AbelMagazine.functionUrl(`/admin/panel`))
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
        h("input", {"type": `text`, "required": ``, "class": `w-full`, "maxlength": `140`, "value": state.full_name, "oninput": this.nameChanged.bind(this)}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.email}`}, [
        h("input", {"type": `email`, "required": ``, "class": `w-full`, "maxlength": `140`, "value": state.email, "oninput": this.emailChanged.bind(this)}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.yourTimezone}`}, [
        h("select", {"onchange": this.changeField.bind(this), "name": `timezone`}, [
          ((AbelMagazine.Time.TimeZones).map((dtz) => (h("option", {"value": dtz, "selected": dtz === this.state.timezone}, `${dtz}`))))
        ])
      ]),
      h("fieldset", {"class": `fieldset`}, [
        h("legend", {}, `${state.lang.forms.changePassword}`),
        h("ext-label", {"label": `${state.lang.forms.currentPassword}`}, [
          h("input", {"type": `password`, "class": `w-full`, "maxlength": `140`, "value": state.currpassword, "oninput": this.currpasswordChanged.bind(this)}, "")
        ]),
        h("ext-label", {"label": `${state.lang.forms.newPassword}`}, [
          h("input", {"type": `password`, "class": `w-full`, "maxlength": `140`, "value": state.password, "oninput": this.passwordChanged.bind(this)}, "")
        ]),
        h("ext-label", {"label": `${state.lang.forms.retypePassword}`}, [
          h("input", {"type": `password`, "class": `w-full`, "maxlength": `140`, "value": state.password2, "oninput": this.password2Changed.bind(this)}, "")
        ])
      ]),
      h("div", {"class": `text-center mt-4`}, [
        h("button", {"class": `btn`, "type": `submit`}, `${state.lang.forms.changeData}`)
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

  
