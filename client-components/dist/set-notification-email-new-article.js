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
        contactEmail: '',
        newArticleEmail: '',
        notifyAuthorArticleApproved: 0,
        notifyAdminFinalArticleUploaded: 0,
        lang: {}
    };

    function setup()
    {
        this.state = 
        { 
            ...this.state, 
            lang: JSON.parse(this.getAttribute('langJson')),
            contactEmail: this.getAttribute('contactEmail'),
            newArticleEmail: this.getAttribute('newArticleEmail'),
            notifyAuthorArticleApproved: this.getAttribute('notifyAuthorArticleApproved'),
            notifyAdminFinalArticleUploaded: this.getAttribute('notifyAdminFinalArticleUploaded'),
        };
    }

    const methods =
    {
        changeField(e)
        {
            if (e.target.type === 'checkbox')
                this.render({ ...this.state, [e.target.name]: Number(e.target.checked) });
            else
                this.render({ ...this.state, [e.target.name]: e.target.value });
        },

        submit(e)
        {
            e.preventDefault();

            const { lang, ...data } = this.state;

            import(AbelMagazine.functionUrl('/admin/panel/articles'))
            .then(module => module.changeNotifyEmail(data))
            .then(AbelMagazine.Alerts.pushFromJsonResult)
            .catch(AbelMagazine.Alerts.pushError(this.state.lang.forms.errorChangingSettings));
        }
    };


  const __template = function({ state }) {
    return [  
    h("form", {"onsubmit": this.submit.bind(this)}, [
      h("ext-label", {"label": `${state.lang.forms.contactEmail}`}, [
        h("input", {"type": `email`, "maxlength": `140`, "name": `contactEmail`, "value": state.contactEmail, "onchange": this.changeField.bind(this), "class": `w-full`}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.adminEmail}`}, [
        h("input", {"type": `email`, "maxlength": `140`, "name": `newArticleEmail`, "value": state.newArticleEmail, "onchange": this.changeField.bind(this), "class": `w-full`}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.notifySubmitterWhenArticleGetsApproved}`, "reverse": `1`}, [
        h("input", {"type": `checkbox`, "value": `1`, "name": `notifyAuthorArticleApproved`, "checked": Boolean(Number(state.notifyAuthorArticleApproved)), "onchange": this.changeField.bind(this), "class": `w-full`}, "")
      ]),
      h("ext-label", {"label": `${state.lang.forms.notifyWhenSubmitterUploadsFinalArticle}`, "reverse": `1`}, [
        h("input", {"type": `checkbox`, "value": `1`, "name": `notifyAdminFinalArticleUploaded`, "checked": Boolean(Number(state.notifyAdminFinalArticleUploaded)), "onchange": this.changeField.bind(this), "class": `w-full`}, "")
      ]),
      h("div", {"class": `text-center mt-2`}, [
        h("button", {"type": `submit`, "class": `btn`}, `${state.lang.forms.save}`)
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

  
