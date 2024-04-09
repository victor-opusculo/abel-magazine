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
        password: "",
        email: "",
        lang: {}
    };

    const methods = 
    {
        async submit(e)
        {
            e.preventDefault();
            
            try
            {
                const { login } = await import(AbelMagazine.functionUrl('/admin'));  
                const result = await login({ data: { email: this.state.email, password: this.state.password } });

                AbelMagazine.Alerts.pushFromJsonResult(result)
                .then(AbelMagazine.Helpers.URLGenerator.goToPageOnSuccess('/admin/panel'));
            }
            catch (err)
            {
                AbelMagazine.Alerts.push(AbelMagazine.Alerts.types.error, this.state.lang.forms.errorLogin);
                console.error(err);
            }
        },

        changeEmail(e)
        {
            this.render({ ...this.state, email: e.target.value });
        },

        changePassword(e)
        {
            this.render({ ...this.state, password: e.target.value }); 
        }
    };

    function setup()
    {
        this.render({ ...this.state, lang: JSON.parse(this.getAttribute('langJson')) });
    }


  const __template = function({ state }) {
    return [  
    h("form", {"class": `mx-auto max-w-[500px]`, "onsubmit": this.submit.bind(this)}, [
      h("ext-label", {"label": `${state.lang.forms.email}`}, [
        h("input", {"type": `email`, "class": `w-full`, "required": ``, "value": state.email, "oninput": this.changeEmail.bind(this)}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.password}`}, [
        h("input", {"type": `password`, "class": `w-full`, "required": ``, "value": state.password, "oninput": this.changePassword.bind(this)}, "")
      ]),
      h("div", {"class": `text-center`}, [
        h("button", {"class": `btn`, "type": `submit`}, `${state.lang.forms.enter}`)
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

  
